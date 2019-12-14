<?php

namespace BalancelleBundle\Controller;

use BalancelleBundle\Entity\Permanence;
use BalancelleBundle\Entity\Semaine;
use BalancelleBundle\Entity\Structure;
use BalancelleBundle\Form\PermanenceCreerType;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use BalancelleBundle\Form\PermanenceType;
use Swift_Message;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Permanence controller.
 *
 */
class PermanenceController extends Controller implements FamilleInterface
{
    /** ------------------------------ FAMILLE ----------------------------- **/
    /**
     * Accès au planning des permanences
     * @param $structure
     * @return Response
     */
    public function indexAction($structure)
    {
        $famille = $this->get('session')->get('famille');
        $this->get('session')->set('structure', $structure);

        return $this->render(
            '@Balancelle/Permanence/permanence.html.twig',
            array('famille' => $famille)
        );
    }

    /**
     * Enregistre l'inscription d'une famille à une permanence
     * @param Request $request
     * @param $id - l'id de la permanence
     * @return RedirectResponse|Response
     * @throws \Exception
     */
    public function inscriptionAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $famille = $em
            ->getRepository('BalancelleBundle:Famille')
            ->findByFamille($this->getUser()->getId());
        $permanence = $em
            ->getRepository('BalancelleBundle:Permanence')
            ->find($id);
        $desinscription = $this->desinscriptionPossible($permanence, $famille);
        $editForm = $this->createForm(
            PermanenceType::class,
            $permanence
        );
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            if ($famille !== null) {
                $permanence->setFamille($famille);
            }
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute(
                'permanence_index',
                array('structure' => $this->get('session')->get('structure'))
            );
        }

        if (!$famille && $permanence->getFamille()) {
            $famille = $permanence->getFamille();
        }
        $listeFamilles = $em
            ->getRepository('BalancelleBundle:Famille')
            ->findBy(['active' => 1]);

        return $this->render(
            '@Balancelle/Permanence/inscription.html.twig',
            array(
                'permanence' => $permanence,
                'inscription_form' => $editForm->createView(),
                'famille' => $famille,
                'listeFamilles' => $listeFamilles,
                'inscriptionPossible' => $this->inscriptionPossible($permanence),
                'desinscriptionPossible' => $desinscription,
                'echangePossible' => $this->echangePossible(
                    $permanence,
                    $famille
                )
            )
        );
    }

    /**
     * Vérifie si l'inscription à une permanence est possible
     * @param Permanence $permanence
     * @return bool
     * @throws \Exception
     */
    private function inscriptionPossible($permanence)
    {
        return ($permanence->getFamille() === null)
            && (
                $this->container->get(
                    'security.authorization_checker'
                )->isGranted('ROLE_ADMIN') ||
                (
                    new DateTime() < new DateTime(
                        $permanence->getDebut()->format('Y-m-d')
                    )
                )
            );
    }

    /**
     * Vérifie si la famille peut se désinscrire d'une permanence
     * @param Permanence $permanence
     * @return bool
     * @throws \Exception
     */
    private function desinscriptionPossible($permanence, $idFamille)
    {
        $date = new DateTime();
        $date->modify(
            '+14 days'
        );
        return ($idFamille === $permanence->getFamille()
            &&
            $date < new DateTime(
                $permanence->getDebut()->format('Y-m-d')
            )
        );
    }

    /**
     * Vérifie si la famille peut demander un échange d'une permanence
     * @param Permanence $permanence
     * @return bool
     * @throws \Exception
     */
    private function echangePossible($permanence, $idFamille)
    {
        $dateMax = new DateTime();
        $dateMax->modify(
            '+14 days'
        );
        $datePermanence = new DateTime(
            $permanence->getDebut()->format('Y-m-d')
        );
        return ($idFamille === $permanence->getFamille()
            && $dateMax > $datePermanence
            && $datePermanence > new DateTime()
            && !$permanence->getEchange()
        );
    }

    /**
     * Permet à une famille de se désinscrire d'une permanence
     * Méthode ajax
     * @param Request $request
     * @return JsonResponse
     */
    public function desinscriptionAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $permanence = $em
            ->getRepository('BalancelleBundle:Permanence')
            ->find($request->get('idPermanence'));
        $permanence->setFamille(null);
        $em->flush();
        $reponse = 'Vous avez a bien été désinscrit de la permanence';
        $type = 'success';
        $this->addFlash($type, $reponse);
        return new JsonResponse('ok');
    }

    /**
     * Action pour échanger une permanence
     * @param Request $request
     * @return JsonResponse
     */
    public function echangeAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $permanence = $em
            ->getRepository('BalancelleBundle:Permanence')
            ->find($request->get('idPermanence'));
        $permanence->setEchange(0);
        $reponse = "Votre permanence n'est plus proposée à l'échange";
        if ($request->get('action') !== 'false' &&
            $request->get('action') !== 'accept') {
            $reponse = "Votre permanence a été proposée à l'échange";
            $permanence->setEchange(1);
        }
        if ($request->get('action') === 'accept') {
            $famille = $em
                ->getRepository('BalancelleBundle:Famille')
                ->findByFamille($this->getUser()->getId());
            $reponse = "Vous êtes désormais inscrit à cette permanence";

            $sujet = "Echange d'une permanence";
            $to = $permanence->getSemaine()->getCalendrier()->getStructure()->getEmail();
            $mail = Swift_Message::newInstance()
                ->setSubject($sujet)
                ->setFrom('comptes@labalancelle.yo.fr')
                ->setTo($to)
                ->setContentType('text/html')
                ->setBody(
                    $this->renderView(
                        '@Balancelle/Permanence/Admin/echange_email.html.twig',
                        array(
                            'famille1' => $permanence->getFamille()->getNom(),
                            'famille2' => $famille->getNom(),
                            'datePerm' => $permanence->getDebut(),
                            'id' => $permanence->getId()
                        )
                    )
                );
            $this->get('mailer')->send($mail);
            $permanence->setFamille($famille);
        }
        $em->flush();
        $type = 'success';
        $this->addFlash($type, $reponse);
        return new JsonResponse('ok');
    }

    /** ------------------------------- ADMIN ------------------------------ **/

    public function tableauDeBordAction()
    {
        $em = $this->getDoctrine()->getManager();
        $familles = $em
            ->getRepository('BalancelleBundle:Famille')
            ->findAll();
        $repository = $this->getDoctrine()->getRepository(Permanence::class);
        foreach ($familles as $famille) {
            $famille->permFaite = $repository->recupererLesPermanencesRealisees(
                $famille
            );
            $famille->nbPermanenceAFaire = $famille->getNombrePermanence();
            $famille->permanenceInscrit = $repository->findByFamille($famille);
            $famille->pourcentagePermanenceFaite = 0;
            if ($famille->nbPermanenceAFaire) {
                $famille->pourcentagePermanenceFaite = count(
                        $famille->permFaite
                    )*100/$famille->nbPermanenceAFaire;
            }
        }
        return $this->render(
            '@Balancelle/Permanence/Admin/index.html.twig',
            array(
                'familles' => $familles,
                /*'toutesLesPermanencesArealiser' =>
                    $repository->recupererToutesLesPermanences(
                        'JEE',
                        date('Y-m-d H:i:s'),
                        null
                    ),
                'toutesLesPermanencesArealiserLibre' =>
                    $repository->recupererToutesLesPermanencesLibre(
                        'JEE',
                        date('Y-m-d H:i:s'),
                        null
                    )*/
            )
        );
    }

    /**
     * Index de l'admin
     * @param Structure $structure - la structure visionnée
     * @return Response
     */
    public function indexAdminAction($structure)
    {
        $this->get('session')->set('structure', $structure);

        return $this->render(
            '@Balancelle/Permanence/permanence.html.twig',
            array('famille' => null)
        );
    }

    /**
     * Ajoute une famille à une permanence
     * @param Request $request
     * @return JsonResponse
     */
    public function ajouterFamilleAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $reponse = "La famille n'a pas été incrite à la permanence";
        $type = 'error';
        $famille = $em
            ->getRepository('BalancelleBundle:Famille')
            ->find($request->get('idFamille'));
        $permanence = $em
            ->getRepository('BalancelleBundle:Permanence')
            ->find($request->get('idPermanence'));
        if ($famille && $permanence) {
            $permanence->setFamille($famille);
            $em->flush();
            $reponse = 'La famille a bien été incrite à la permanence';
            $type = 'success';
        }

        $this->addFlash($type, $reponse);
        return new JsonResponse($reponse);
    }

    /**
     * Permet de désinscrire une famille d'une permanence
     * Méthode ajax
     * @param Request $request
     * @return JsonResponse
     */
    public function desinscrireFamilleAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $permanence = $em
            ->getRepository('BalancelleBundle:Permanence')
            ->find($request->get('idPermanence'));
        $permanence->setFamille(null);
        $em->flush();
        $reponse = 'La famille a bien été désinscrite de la permanence';
        $type = 'success';
        $this->addFlash($type, $reponse);
        return new JsonResponse('ok');
    }

    /**
     * Méthode pour supprimer une permanence
     * Méthode ajax
     * @param Request $request
     * @return JsonResponse
     */
    public function supprimerPermanenceAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $permanence = $em
            ->getRepository('BalancelleBundle:Permanence')
            ->find($request->get('idPermanence'));
        $em->remove($permanence);
        $em->flush();
        $reponse = 'La permanence a bien été supprimée';
        $type = 'success';
        $this->addFlash($type, $reponse);
        $url = $this->generateUrl(
            'admin_permanence_structure',
            array('structure' => $this->get('session')->get('structure'))
        );

        return new JsonResponse($url);
    }

    /**
     * Méthode pour créer une permanence
     * Méthode ajax
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function creerPermanenceAction(
        Request $request
    ) {
        $em = $this->getDoctrine()->getManager();

        $structure = $em->getRepository(Structure::class)
            ->findOneBy(
                ['nomCourt' => $this->get('session')->get('structure')]
            );

        if ($request->get('semaineId')!== null &&
            $request->get('date')!== null) {
            $permanence = new Permanence();
            $permanence->setActive(true);
            $permanence->setTitre('Nouvelle permanence');
            $permanence->setCommentaire("Création d'une nouvelle permanence");
            $permanence->setDebut(
                new DateTime(
                    $request->get('date')
                ));
            $permanence->setFin(
                new DateTime(
                    $request->get('date')
                ));
            $permanence->setSemaine(
                $em->getRepository(Semaine::class)
                   ->find($request->get('semaineId'))
            );
            $permanence->setCouleur('#567c3f');
            $createForm = $this->createForm(
                PermanenceCreerType::class,
                $permanence
            );
        }

        $createForm->handleRequest($request);
        if ($createForm->isSubmitted() && $createForm->isValid()) {
            $date = explode('-', $request->get('date'));
            $dtoDebut = new DateTime($request->get('date'));
            $dtoFin = new DateTime($request->get('date'));
            $heureDebut = explode(
                '-',
                $structure->getHeureDebutPermanenceMatin()
            );
            $heureFin = explode(
                '-',
                $structure->getHeureFinPermanenceMatin()
            );
            if ($request->get('demiejournee') === "amPermanence") {
                $heureDebut = explode(
                    '-',
                    $structure->getHeureDebutPermanenceAM()
                );
                $heureFin = explode('-', $structure->getHeureFinPermanenceAM());
            }
            $permanence->setDebut(
                $dtoDebut->setTime($heureDebut[0], $heureDebut[1])
            );
            $permanence->setFin(
                $dtoFin->setTime($heureFin[0], $heureFin[1])
            );

            $em->persist($permanence);
            $this->getDoctrine()->getManager()->flush();
            $succes = 'La permanence  ';
            $succes .= ' a bien été enregistré';
            $this->addFlash('success', $succes);
            return $this->redirectToRoute(
                'permanence_inscription',
                array('id' => $permanence->getId())
            );
        }

        return $this->render(
            '@Balancelle/Permanence/Admin/creer.html.twig',
            array(
                'permanence' => $permanence,
                'create_form' => $createForm->createView(),
                'structure' => $structure
            )
        );
    }

    public function rappelAction(Request $request, Permanence $permanence)
    {
        $url = $this->generateUrl(
            'permanence_inscription',
            array('id' => $permanence->getId()),
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $structure = $permanence->getSemaine()->getCalendrier()->getStructure();
        $sujet = 'Rappel permamanence ';
        $sujet .= $structure->getNom();
        $message = 'Bonjour à tous. </br>';
        $message .= 'Il n\'y a toujours pas d\'inscrit pour la permanence de ';
        $message .= $permanence->getDebut()->format('d/m/Y H:i:s') . '.</br>';
        $message .= 'Je vous rappelle que votre présence est primordiale ';
        $message .= 'pour assurer  un acceuil de qualité à vos enfants.</br>';
        $message .= 'Vous pouvez vous inscrire sur le site internet en cliquant';
        $message .= ' sur ce  <a href="'. $url . '">lien</a> ';
        $message .= 'ou appeler la structure directement. </br>';
        $message .= 'Je vous remercie d\'avance';

        $this->get('communication')->envoyerMailStructure(
            $structure,
            $sujet,
            $message
        );

        return new JsonResponse('ok');
    }
}
