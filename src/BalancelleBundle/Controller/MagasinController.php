<?php

namespace BalancelleBundle\Controller;

use BalancelleBundle\Entity\Magasin;
use BalancelleBundle\Form\MagasinType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MagasinController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $magasins = $em->getRepository('BalancelleBundle:Magasin')->findAll();
        return $this->render('@Balancelle/Magasin/index.html.twig', array(
            'magasins' => $magasins,
        ));
    }

    /**
     * Modification d'un calendrier
     * @param Request $request
     * @param Magasin $magasin
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, Magasin $magasin)
    {
        $deleteForm = $this->createDeleteForm($magasin);
        $editForm = $this->createForm(
            MagasinType::class,
            $magasin
        );
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $succes = 'Le magasin ';
            $succes .= ' a bien été modifié';
            $this->addFlash('success', $succes);
            return $this->redirectToRoute(
                'magasin_edit',
                array('id' => $magasin->getId())
            );
        }

        return $this->render(
            '@Balancelle/Magasin/edit.html.twig',
            array(
                'magasin' => $magasin,
                'form' => $editForm->createView(),
                'delete_form' => $deleteForm->createView()
            )
        );
    }

    /**
     * Créer un magasin
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $magasin = new Magasin();
        $form = $this->createForm(MagasinType::class, $magasin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($magasin);
            $em->flush();
            $succes = 'Le magasin  ';
            $succes .= ' a bien été enregistré';
            $this->addFlash('success', $succes);

            return $this->redirectToRoute(
                'magasin_edit',
                array('id' => $magasin->getId())
            );
        }

        return $this->render(
            '@Balancelle/Magasin/edit.html.twig',
            array(
                'magasin' => $magasin,
                'form' => $form->createView()
            )
        );
    }

    /**
     * Creates a form to delete a structure entity.
     *
     * @param Magasin $magasin The structure entity
     *
     * @return FormInterface
     */
    private function createDeleteForm(Magasin $magasin)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl(
                'magasin_delete',
            array('id' => $magasin->getId())
            ))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }

    /**
     * Deletes a structure entity.
     * @param Request $request
     * @param Magasin $magasin
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, Magasin $magasin)
    {
        $form = $this->createDeleteForm($magasin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($magasin);
            $em->flush();
            $succes = 'Le magasin ';
            $succes .= ' a bien été supprimée';
            $this->addFlash('success', $succes);
        }

        return $this->redirectToRoute('magasin_index');
    }
}
