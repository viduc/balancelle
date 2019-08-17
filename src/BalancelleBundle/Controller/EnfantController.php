<?php

namespace BalancelleBundle\Controller;

use BalancelleBundle\Entity\Enfant;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use BalancelleBundle\Form\EnfantType;
use Symfony\Component\HttpFoundation\Response;

/**
 * Enfant controller.
 *
 */
class EnfantController extends Controller
{
    
    /**
     * Lists all enfant entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $enfants = $em->getRepository('BalancelleBundle:Enfant')->findAll();

        return $this->render('@Balancelle/Enfant/index.html.twig', array(
            'enfants' => $enfants
        ));
    }

    /**
     * Creates a new enfant entity.
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function newAction(Request $request)
    {
        $enfant = new Enfant();
        $form = $this->createForm(EnfantType::class, $enfant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($enfant);
            $em->flush();

            return $this->redirectToRoute(
                'enfant_edit',
                array('id' => $enfant->getId())
            );
        }

        return $this->render('@Balancelle/Enfant/new.html.twig', array(
            'enfant' => $enfant,
            'form' => $form->createView()
        ));
    }

    /**
     * Displays a form to edit an existing enfant entity.
     * @param Request $request
     * @param Enfant $enfant
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, Enfant $enfant)
    {
        $deleteForm = $this->createDeleteForm($enfant);
        $editForm = $this->createForm(
            EnfantType::class,
            $enfant
        );
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute(
                'enfant_edit',
                array('id' => $enfant->getId())
            );
        }

        return $this->render('@Balancelle/Enfant/edit.html.twig', array(
            'enfant' => $enfant,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView()
        ));
    }

    /**
     * Deletes a enfant entity.
     * @param Request $request
     * @param Enfant $enfant
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, Enfant $enfant)
    {
        $form = $this->createDeleteForm($enfant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($enfant);
            $em->flush();
        }

        return $this->redirectToRoute('enfant_index');
    }

    /**
     * Creates a form to delete a enfant entity.
     *
     * @param Enfant $enfant The enfant entity
     *
     * @return FormInterface
     */
    private function createDeleteForm(Enfant $enfant)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl(
                'enfant_delete',
                array('id' => $enfant->getId()
            )))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * Méthode d'autocomplétion pour les enfants
     * @param Request $request - la requete
     * @return JsonResponse|null
     */
    public function autocompleteAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $enfants = $em
                ->getRepository('BalancelleBundle:Enfant')
                ->autocomplete($request->get('recherche'));
            $tabResponse = [];
            foreach ($enfants as $enfant) {
                //$tab["prenom"] = ucfirst($enfant->getPrenom());
                //$tab["nom"] = ucfirst($enfant->getNom());
                //$tab["id"] = $enfant->getId();

                $tab['label'] = ucfirst($enfant->getPrenom()) . ' ';
                $tab['label'] .= ucfirst($enfant->getNom());
                $tab['value'] = $enfant->getId();
                $tabResponse[] = $tab;
            }
            return new JsonResponse($tabResponse);
        }
        return null;
    }
}
