<?php

namespace BalancelleBundle\Controller;

use BalancelleBundle\Entity\Annee;
use BalancelleBundle\Form\AnneeType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AnneeController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $annees = $em->getRepository('BalancelleBundle:Annee')->findAll();
        return $this->render(
            '@Balancelle/Annee/index.html.twig',
            array('annees' => $annees)
        );
    }

    /**
     * Modification d'une annee
     * @param Request $request
     * @param Course $course
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, Annee $annee)
    {
        $deleteForm = $this->createDeleteForm($annee);
        $editForm = $this->createForm(
            AnneeType::class,
            $annee
        );
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $succes = 'L\' annéee ';
            $succes .= ' a bien été modifiée';
            $this->addFlash('success', $succes);
            return $this->redirectToRoute(
                'annee_edit',
                array('id' => $annee->getId())
            );
        }

        return $this->render(
            '@Balancelle/Annee/edit.html.twig',
            array(
                'annee' => $annee,
                'form' => $editForm->createView(),
                'delete_form' => $deleteForm->createView()
            )
        );
    }

    /**
     * Créer une année
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $annee = new Annee();
        $form = $this->createForm(AnneeType::class, $annee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($annee);
            $em->flush();
            $succes = 'L\'année a bien été enregistrée';
            $this->addFlash('success', $succes);

            return $this->redirectToRoute(
                'annee_edit',
                array('id' => $annee->getId())
            );
        }

        return $this->render(
            '@Balancelle/Annee/edit.html.twig',
            array(
                'annee' => $annee,
                'form' => $form->createView()
            )
        );
    }

    /**
     * Creates a form to delete a structure entity.
     *
     * @param Annee $annee The annee entity
     *
     * @return FormInterface
     */
    private function createDeleteForm(Annee $annee)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl(
                'annee_delete',
            array('id' => $annee->getId())
            ))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }

    /**+
     * Deletes a annee entity.
     * @param Request $request
     * @param Annee $annee
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, Annee $annee)
    {
        $form = $this->createDeleteForm($annee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($annee);
            $em->flush();
            $succes = 'L\' année';
            $succes .= ' a bien été supprimée';
            $this->addFlash('success', $succes);
        }

        return $this->redirectToRoute('annee_index');
    }
}
