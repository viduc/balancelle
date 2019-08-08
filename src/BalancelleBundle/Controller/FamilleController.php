<?php

namespace BalancelleBundle\Controller;

use BalancelleBundle\Entity\Enfant;
use BalancelleBundle\Entity\Famille;
use BalancelleBundle\Entity\Permanence;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;

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
     * @return Response
     */
    public function tableauDeBordAction()
    {
        $pourcentagePermanenceFaite = null;
        $nbPermanenceAFaire = null;
        $permFaite = null;
        $permanenceInscrit = null;
        /** @var Famille $famille */
        $famille = $this->get('session')->get('famille');
        if ($famille) {
            $repository = $this->getDoctrine()->getRepository(Permanence::class);
            $permFaite = $repository->recupererLesPermanencesRealisees(
                $famille
            );
            $nbPermanenceAFaire = $famille->getNombrePermanence();
            $permanenceInscrit = $repository->findByFamille($famille);
            $pourcentagePermanenceFaite = count(
                $permFaite
            )*100/$nbPermanenceAFaire;
        } else {
            return $this->redirectToRoute(
                'famille_erreurFamille',
                array());
        }
        return $this->render(
            '@Balancelle/famille/tableauDeBord.html.twig',
            array(
                'famille' => $famille,
                'parents' => $this->getParents($famille),
                'pourcentagePermanenceFaite' =>$pourcentagePermanenceFaite,
                'nbPermanenceAFaire' => $nbPermanenceAFaire,
                'permanenceFaite' => $permFaite,
                'permanenceInscrit' => count(
                    $permanenceInscrit
                ) - count($permFaite),
                'listePermanence' => $this->formaterListePermanence(
                    $permanenceInscrit,
                    $permFaite
                )
            )
        );
    }

    public function erreurFamilleAction()
    {
        $this->addFlash("error", "Vous n'êtes pas inscrit dans une famille");
        return $this->render(
            '@Balancelle/famille/erreur_famille.html.twig',
            array()
        );
    }

    /**
     * Formate la liste des permanences réalisée.
     * @param $permanenceInscrit
     * @param $permFaite
     * @return array
     */
    private function formaterListePermanence($permanenceInscrit, $permFaite)
    {
        $retour = [];
        foreach($permanenceInscrit as $perm) {
            $perm->realise = in_array($perm, $permFaite);
            $retour[] = $perm;
        }

        return $retour;
    }

    /**
     * Liste toutes les familles
     * @return \Symfony\Component\HttpFoundation\Response
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
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function newAction(Request $request)
    {
        $famille = new Famille();
        $form = $this->createForm('BalancelleBundle\Form\FamilleType', $famille);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $famille->setDateCreation(new \DateTime());
            $famille->setDateModification(new \DateTime());
            $this->em->persist($famille);
            $this->em->flush();
            $succes = "La famille " . $famille->getNom() . " ";
            $succes .= " a bien été enregistrée";
            $this->addFlash("success", $succes);

            return $this->redirectToRoute(
                'famille_edit',
                array('id' => $famille->getId()
            ));
        }

        return $this->render('@Balancelle/famille/edit.html.twig', array(
            'famille' => $famille,
            'form' => $form->createView(),
            'enfants' => null,
            'listeEnfants' => null,
            'errors' => null
        ));
    }

    /**
     * Edite une famille
     * @param Request $request
     * @param Famille $famille
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Famille $famille)
    {
        $editForm = $this->createForm(
            'BalancelleBundle\Form\FamilleType',
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
        return $this->render('@Balancelle/famille/edit.html.twig', array(
            'familleAdmin' => $famille,
            'form' => $editForm->createView(),
            'enfants' => $famille->getEnfants(),
            'listeEnfants' => $this->em->getRepository(
                'BalancelleBundle:Enfant'
            )->getEnfantSansFamille(),
            'delete_form' => $deleteForm->createView(),
            'structures' => $structures
        ));
    }

    /**
     * Supprime une famille
     * @param Request $request - la requete
     * @param Famille $famille - la famille
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
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
     * @return \Symfony\Component\Form\FormInterface
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
            $reponse .= $enfant->getPrenom() . " " . $enfant->getNom();
            $reponse .= ' a bien été ajouté à la famille ';
            $reponse .= $famille->getNom();
        }
        $this->addFlash($type,$reponse);
        return new JsonResponse($reponse);
    }

    /**
     * méthode ajax qui permet de creer un enfant pour une famille
     * @param Request $request - la requete
     * @return JsonResponse
     * @throws \Exception
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
            new \DateTime($request->get('dateNaissanceEnfant')
        ));
        $enfant->setFamille($famille);
        $enfant->setCommentaire("Enfant créer via l'interface famille");
        $enfant->setActive(true);
        $enfant->setStructure(
            $this->em->getRepository('BalancelleBundle:Structure')->find($request->get('idStructure'))
        );
        $this->em->persist($enfant);
        $this->em->flush();
        $reponse = "L'enfant ";
        $reponse .= $enfant->getPrenom() . ' ' . $enfant->getNom();
        $reponse .= ' a bien été ajouté à la famille ';
        $reponse .= $famille->getNom();
        $this->addFlash('success',$reponse);
        return new JsonResponse($reponse);
    }

    /**
     * méthode ajax qui permet d'ajouter un enfant à une famille
     * Si l'enfant appartient déjà une famille, l'opération n'aura pas lieu
     * @param int $idEnfant - l'id de l'enfant
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
            $reponse .= $enfant->getPrenom() . " " . $enfant->getNom();
            $reponse .= ' a bien été supprimer de la famille ';
            $type = 'success';
        }
        $this->addFlash($type,$reponse);
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

        if($famille) {
            if ($famille->getParent1() !== null) {
                $parent['parent1'] = $this->em->getRepository(
                    'BalancelleBundle:User'
                )->findOneById($famille->getParent1()->getId())
                ;
            }
            $parent['parent2'] = null;
            if ($famille->getParent2() !== null) {
                $parent[] = $this->em->getRepository(
                    'BalancelleBundle:User'
                )->findOneById($famille->getParent2()->getId());
            }
        }
        return $parent;
    }
}
