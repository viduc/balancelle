<?php

namespace BalancelleBundle\Controller;

use BalancelleBundle\Entity\Semaine;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Semaine controller.
 *
 */
class SemaineController extends Controller
{
    /**
     * Lists all semaine entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $semaines = $em->getRepository('BalancelleBundle:Semaine')->findAll();

        return $this->render('semaine/index.html.twig', array(
            'semaines' => $semaines,
        ));
    }

    /**
     * Creates a new semaine entity.
     *
     */
    public function newAction(Request $request)
    {
        $semaine = new Semaine();
        $form = $this->createForm('BalancelleBundle\Form\SemaineType', $semaine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($semaine);
            $em->flush();

            return $this->redirectToRoute('semaine_show', array('id' => $semaine->getId()));
        }

        return $this->render('semaine/new.html.twig', array(
            'semaine' => $semaine,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a semaine entity.
     *
     */
    public function showAction(Semaine $semaine)
    {
        $deleteForm = $this->createDeleteForm($semaine);

        return $this->render('semaine/show.html.twig', array(
            'semaine' => $semaine,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing semaine entity.
     *
     */
    public function editAction(Request $request, Semaine $semaine)
    {
        $deleteForm = $this->createDeleteForm($semaine);
        $editForm = $this->createForm('BalancelleBundle\Form\SemaineType', $semaine);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('semaine_edit', array('id' => $semaine->getId()));
        }

        return $this->render('semaine/edit.html.twig', array(
            'semaine' => $semaine,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a semaine entity.
     *
     */
    public function deleteAction(Request $request, Semaine $semaine)
    {
        $form = $this->createDeleteForm($semaine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($semaine);
            $em->flush();
        }

        return $this->redirectToRoute('semaine_index');
    }

    /**
     * Creates a form to delete a semaine entity.
     *
     * @param Semaine $semaine The semaine entity
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createDeleteForm(Semaine $semaine)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl(
                'semaine_delete',
                array('id' => $semaine->getId()
            )))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
