<?php

namespace BalancelleBundle\Controller;

use BalancelleBundle\Entity\Semaine;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use BalancelleBundle\Form\SemaineType;
use Symfony\Component\HttpFoundation\Response;

/**
 * Semaine controller.
 *
 */
class SemaineController extends AppController
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
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function newAction(Request $request)
    {
        $semaine = new Semaine();
        $form = $this->createForm(SemaineType::class, $semaine);
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
     * @param Semaine $semaine
     * @return Response
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
     * @param Request $request
     * @param Semaine $semaine
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, Semaine $semaine)
    {
        $deleteForm = $this->createDeleteForm($semaine);
        $editForm = $this->createForm(SemaineType::class, $semaine);
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
     * @param Request $request
     * @param Semaine $semaine
     * @return RedirectResponse
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
     * @return FormInterface
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
