<?php

namespace BalancelleBundle\Controller;

use BalancelleBundle\Entity\Enfant;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Enfant controller.
 *
 */
class EnfantController extends Controller
{
    private $view = 'enfant';
    
    /**
     * Lists all enfant entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $enfants = $em->getRepository('BalancelleBundle:Enfant')->findAll();

        return $this->render('@Balancelle/Enfant/index.html.twig', array(
            'enfants' => $enfants,'view' => $this->view
        ));
    }

    /**
     * Creates a new enfant entity.
     *
     */
    public function newAction(Request $request)
    {
        $enfant = new Enfant();
        $form = $this->createForm('BalancelleBundle\Form\EnfantType', $enfant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($enfant);
            $em->flush();

            return $this->redirectToRoute('enfenfant_edit', array('id' => $enfant->getId()));
        }

        return $this->render('@Balancelle/Enfant/new.html.twig', array(
            'enfant' => $enfant,
            'form' => $form->createView(),
            'view' => $this->view
        ));
    }

    /**
     * Displays a form to edit an existing enfant entity.
     *
     */
    public function editAction(Request $request, Enfant $enfant)
    {
        $deleteForm = $this->createDeleteForm($enfant);
        $editForm = $this->createForm('BalancelleBundle\Form\EnfantType', $enfant);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('enfant_edit', array('id' => $enfant->getId()));
        }

        return $this->render('@Balancelle/Enfant/edit.html.twig', array(
            'enfant' => $enfant,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'view' => $this->view
        ));
    }

    /**
     * Deletes a enfant entity.
     *
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
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Enfant $enfant)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('enfant_delete', array('id' => $enfant->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
