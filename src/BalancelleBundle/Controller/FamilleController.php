<?php

namespace BalancelleBundle\Controller;

use BalancelleBundle\Entity\Enfant;
use BalancelleBundle\Entity\Famille;
use BalancelleBundle\Entity\Permanence;
use BalancelleBundle\Entity\Structure;
use BalancelleBundle\Form\MailType;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use BalancelleBundle\Form\FamilleType;

/**
 * Famille controller.
 *
 */
class FamilleController extends Controller implements FamilleInterface
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Permet de récupérer toutes les finformations des permanences pour une
     * famille
     * @param Famille $famille - la famille
     * @return array|null - un tableau contenant les informations
     */
    private function getInfoPermanenceFamille(Famille $famille)
    {
        $permanence = null;
        /** @var Famille $famille */
        if ($famille) {
            $repository = $this->getDoctrine()->getRepository(Permanence::class);
            $permanence['faite'] = $repository->recupererLesPermanencesRealisees(
                $famille
            );
            $permanence['aFaire'] = $famille->getNombrePermanence();
            $permanence['inscrit'] = $repository->findByFamille($famille);
            $permanence['pourcentage'] = 0;
            if ($permanence['aFaire']) {
                $permanence['pourcentage'] = count(
                        $permanence['faite']
                    )*100/$permanence['aFaire'];
            }
        }
        return $permanence;
    }

    /**
     * Tableau de bord pour les familles
     * @return Response
     */
    public function tableauDeBordAction()
    {
        /** @var Famille $famille */
        $famille = $this->get('session')->get('famille');
        if (!$famille) {
            return $this->redirectToRoute(
                'famille_erreurFamille',
                array()
            );
        }
        $permanences = $this->getInfoPermanenceFamille($famille);
        return $this->render(
            '@Balancelle/famille/tableauDeBord.html.twig',
            array(
                'famille' => $famille,
                'parents' => $this->getParents($famille),
                'permanences' => $permanences,
                'listePermanence' => $this->formaterListePermanence($permanences)
            )
        );
    }

    public function erreurFamilleAction()
    {
        $this->addFlash('error', "Vous n'êtes pas inscrit dans une famille");
        return $this->render(
            '@Balancelle/famille/erreur_famille.html.twig',
            array()
        );
    }

    /**
     * Formate la liste des permanences réalisée.
     * @param $permanences - la tableau des information de permanences
     * @return array
     */
    private function formaterListePermanence($permanences)//$permanenceInscrit, $permFaite)
    {
        $retour = [];
        foreach ($permanences['inscrit'] as $perm) {
            $perm->realise = in_array($perm, $permanences['faite'], true);
            $retour[] = $perm;
        }

        return $retour;
    }

    /**
     * Liste toutes les familles
     * @return Response
     */
    public function indexAction()
    {
        $familles = $this->em->getRepository('BalancelleBundle:Famille')->findAll();

        return $this->render(
            '@Balancelle/famille/index.html.twig',
            array(
                'familles' => $familles
            )
        );
    }

    /**
     * Créé une famille
     * @param Request $request
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function newAction(Request $request)
    {
        $famille = new Famille();
        $form = $this->createForm(FamilleType::class, $famille);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $famille->setDateCreation(new DateTime());
            $famille->setDateModification(new DateTime());
            $this->em->persist($famille);
            $this->em->flush();
            $succes = 'La famille ' . $famille->getNom() . ' ';
            $succes .= ' a bien été enregistrée';
            $this->addFlash('success', $succes);

            return $this->redirectToRoute(
                'famille_edit',
                array('id' => $famille->getId()
            ));
        }

        return $this->render('@Balancelle/famille/edit.html.twig', array(
            'familleAdmin' => $famille,
            'form' => $form->createView(),
            'enfants' => null,
            'listeEnfants' => null,
            'errors' => null,
            'structures' => $this->em->getRepository('BalancelleBundle:Structure')
                 ->findBy(['active'=>1])
        ));
    }

    /**
     * Edite une famille
     * @param Request $request
     * @param Famille $famille
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, Famille $famille)
    {
        $editForm = $this->createForm(
            FamilleType::class,
            $famille
        );
        $deleteForm = $this->createDeleteForm($famille);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $succes = 'La famille ' . $famille->getNom() . ' ';
            $succes .= ' a bien été modifiée';
            $this->addFlash('success', $succes);
        }
        $structures = $this->em->getRepository('BalancelleBundle:Structure')->findBy(['active'=>1]);
        $permanences = $this->getInfoPermanenceFamille($famille);
        return $this->render('@Balancelle/famille/edit.html.twig', array(
            'familleAdmin' => $famille,
            'form' => $editForm->createView(),
            'enfants' => $famille->getEnfants(),
            'listeEnfants' => $this->em->getRepository(
                'BalancelleBundle:Enfant'
            )->getEnfantSansFamille(),
            'delete_form' => $deleteForm->createView(),
            'structures' => $structures,
            'permanences' => $permanences,
            'listePermanence' => $this->formaterListePermanence($permanences)
        ));
    }

    /**
     * Supprime une famille
     * @param Request $request - la requete
     * @param Famille $famille - la famille
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, Famille $famille)
    {
        $form = $this->createDeleteForm($famille);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->remove($famille);
            $this->em->flush();
            $succes = 'La famille ';
            $succes .= ' a bien été supprimée';
            $this->addFlash('success', $succes);
        }

        return $this->redirectToRoute('famille_index');
    }

    /**
     * Creates a form to delete a famille entity.
     *
     * @param Famille $famille The famille entity
     *
     * @return FormInterface
     */
    private function createDeleteForm(Famille $famille)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl(
                'famille_delete',
                array('id' => $famille->getId()
            )))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * méthode ajax qui permet d'ajouter un enfant à une famille
     * Si l'enfant appartient déjà une famille, l'opération n'aura pas lieu
     * @param Request $request - la requete
     * @return JsonResponse
     */
    public function ajouterEnfantAction(Request $request)
    {
        $enfant = $this->em
            ->getRepository('BalancelleBundle:Enfant')
            ->find($request->get('idEnfant'));
        $type = 'warning';
        $reponse = 'Aucune famille ne correspond à la demande';
        if ($enfant->getFamille() === null) {
            $famille = $this->em
                ->getRepository('BalancelleBundle:Famille')
                ->find($request->get('idFamille'));
            $enfant->setFamille($famille);
            $this->em->persist($enfant);
            $this->em->flush();
            $type = 'success';
            $reponse = "L'enfant ";
            $reponse .= $enfant->getPrenom() . ' ' . $enfant->getNom();
            $reponse .= ' a bien été ajouté à la famille ';
            $reponse .= $famille->getNom();
        }
        $this->addFlash($type, $reponse);
        return new JsonResponse($reponse);
    }

    /**
     * méthode ajax qui permet de creer un enfant pour une famille
     * @param Request $request - la requete
     * @return JsonResponse
     * @throws Exception
     */
    public function creerEnfantAction(Request $request)
    {
        $famille = $this->em
            ->getRepository('BalancelleBundle:Famille')
            ->find($request->get('idFamille'));

        $enfant = new Enfant();
        $enfant->setPrenom($request->get('prenomEnfant'));
        $enfant->setNom($request->get('nomEnfant'));
        $enfant->setNaissance(
            DateTime::createFromFormat(
                'j/m/Y',
                $request->get('dateNaissanceEnfant')
            )
        );
        $enfant->setFamille($famille);
        $enfant->setCommentaire("Enfant créer via l'interface famille");
        $enfant->setActive(true);
        $enfant->setStructure(
            $this->em->getRepository('BalancelleBundle:Structure')->find(
                $request->get('idStructure')
            )
        );
        $this->em->persist($enfant);
        $this->em->flush();
        $reponse = "L'enfant ";
        $reponse .= $enfant->getPrenom() . ' ' . $enfant->getNom();
        $reponse .= ' a bien été ajouté à la famille ';
        $reponse .= $famille->getNom();
        $this->addFlash('success', $reponse);
        return new JsonResponse($reponse);
    }

    /**
     * méthode ajax qui permet d'ajouter un enfant à une famille
     * Si l'enfant appartient déjà une famille, l'opération n'aura pas lieu
     * @param int $idEnfant - l'id de l'enfant
     * @param $idFamille
     * @return Response
     */
    public function supprimerEnfantAction($idEnfant, $idFamille)
    {
        $type = 'warning';
        $reponse = 'Aucune famille ne correspond à la demande';
        $enfant = $this->em
            ->getRepository('BalancelleBundle:Enfant')
            ->find($idEnfant);
        if ($enfant->getFamille() !== null) {
            $enfant->setFamille(null);
            $this->em->persist($enfant);
            $this->em->flush();
            $reponse = "L'enfant ";
            $reponse .= $enfant->getPrenom() . ' ' . $enfant->getNom();
            $reponse .= ' a bien été supprimer de la famille ';
            $type = 'success';
        }
        $this->addFlash($type, $reponse);
        return $this->forward(
            'BalancelleBundle:Famille:edit',
            [
                'famille' => $this->em
                    ->getRepository('BalancelleBundle:Famille')
                    ->find($idFamille)
            ]
        );
    }

    /**
     * Récupère les parents d'une famille
     * @param $famille - la famille
     * @return array
     */
    public function getParents($famille)
    {
        $parent['parent1'] = null;
        $parent['parent2'] = null;

        if ($famille) {
            if ($famille->getParent1() !== null) {
                $parent['parent1'] = $this->em->getRepository(
                    'BalancelleBundle:User'
                )->findOneById($famille->getParent1()->getId())
                ;
            }
            if ($famille->getParent2() !== null) {
                $parent['parent2'] = $this->em->getRepository(
                    'BalancelleBundle:User'
                )->findOneById($famille->getParent2()->getId());
            }
        }
        return $parent;
    }

    /**
     * Méthode d'autocomplétion pour les familles
     * @param Request $request - la requete
     * @return JsonResponse | null
     */
    public function autocompleteAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $familles = $em
                ->getRepository('BalancelleBundle:Famille')
                ->autocomplete($request->get('recherche'));
            $tabResponse = [];
            foreach ($familles as $famille) {
                $tab['label'] = ucfirst($famille->getNom());
                $tab['value'] = $famille->getId();
                $tabResponse[] = $tab;
            }
            return new JsonResponse($tabResponse);
        }
        return null;
    }

    /**
     * Envoie un mail aux parents de la structure
     * @param Request $request
     * @param Famille $famille - la famille concernée
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function envoyerMailAction(Request $request, Famille $famille)
    {
        $form = $this->createForm(
            MailType::class
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $documents = $form->getData()['documents'];
            array_pop($documents);
            $tabMails[] = $famille->getParent1()->getEmail();
            if ($famille->getParent2()) {
                $tabMails[] = $famille->getParent2()->getEmail();
            }
            $this->get('communication')->envoyerMail(
                $tabMails,
                $form->getData()['sujet'],
                $form->getData()['message'],
                $documents
            );
            $succes = 'Votre email a bien été envoyé à la famille';
            $this->addFlash('success', $succes);
            return $this->redirectToRoute(
                'famille_edit',
                array('id' => $famille->getId())
            );
        }
        $titre = 'Envoyer un email à la famille ';
        $titre .= $famille->getNom();
        return $this->render(
            '@Balancelle/Communication/email_create.html.twig',
            array(
                'form' => $form->createView(),
                'titre' => $titre
            )
        );
    }
}
