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
                'inscriptionPossible' => $this->inscriptionPossible($permanence)
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
            $famille->pourcentagePermanenceFaite = count(
                    $famille->permFaite
                )*100/$famille->nbPermanenceAFaire;
        }
        return $this->render(
            '@Balancelle/Permanence/Admin/index.html.twig',
            array(
                'familles' => $familles,
                'toutesLesPermanencesArealiser' =>
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
                    )
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
}
