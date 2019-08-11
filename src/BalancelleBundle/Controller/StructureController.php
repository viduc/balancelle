<?php

namespace BalancelleBundle\Controller;

use BalancelleBundle\Entity\Structure;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Structure controller.
 *
 */
class StructureController extends Controller implements FamilleInterface
{
    private $view = 'structure';

    /**
     * Liste toutes les structures
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $structures = $em->getRepository('BalancelleBundle:Structure')->findAll();

        return $this->render('@Balancelle/Structure/index.html.twig', array(
            'structures' => $structures,'view' => $this->view
        ));
    }

    /**
     * Créé une structure
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $structure = new Structure();
        $form = $this->createForm(
            'BalancelleBundle\Form\StructureType',
            $structure
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($structure);
            $em->flush();
            $succes = 'La structure ' . $structure->getnom() . " ";
            $succes .= ' a bien été enregistrée';
            $this->addFlash('success', $succes);

            return $this->redirectToRoute(
                'structure_edit',
                array('id' => $structure->getId())
            );
        }

        return $this->render(
            '@Balancelle/Structure/edit.html.twig',
            array(
                'structure' => $structure,
                'form' => $form->createView()
            )
        );
    }

    /**
     * Displays a form to edit an existing structure entity.
     *
     */
    public function editAction(Request $request, Structure $structure)
    {
        $deleteForm = $this->createDeleteForm($structure);
        $form = $this->createForm(
            'BalancelleBundle\Form\StructureType',
            $structure);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $succes = 'La structure ' . $structure->getnom() . " ";
            $succes .= ' a bien été modifiée';
            $this->addFlash('success', $succes);
            return $this->redirectToRoute(
                'structure_edit',
                array('id' => $structure->getId())
            );
        }

        return $this->render('@Balancelle/Structure/edit.html.twig',
            array(
                'structure' => $structure,
                'form' => $form->createView(),
                'delete_form' => $deleteForm->createView()
            )
        );
    }

    /**
     * Deletes a structure entity.
     *
     */
    public function deleteAction(Request $request, Structure $structure)
    {
        $form = $this->createDeleteForm($structure);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($structure);
            $em->flush();
            $succes = 'La structure ';
            $succes .= ' a bien été supprimée';
            $this->addFlash('success', $succes);
        }

        return $this->redirectToRoute('structure_index');
    }

    /**
     * Creates a form to delete a structure entity.
     *
     * @param Structure $structure The structure entity
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createDeleteForm(Structure $structure)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl(
                'structure_delete',
                array('id' => $structure->getId())
            ))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
