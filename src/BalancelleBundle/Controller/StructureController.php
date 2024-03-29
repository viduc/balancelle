<?php

namespace BalancelleBundle\Controller;

use BalancelleBundle\Entity\Structure;
//use Symfony\Bundle\MakerBundle\Str;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use BalancelleBundle\Form\StructureType;
use BalancelleBundle\Form\MailType;

/**
 * Structure controller.
 *
 */
class StructureController extends AppController implements FamilleInterface
{
    private $view = 'structure';

    /**
     * Liste toutes les structures
     * @return Response
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
     * @return RedirectResponse|Response
     */
    public function newAction(Request $request)
    {
        $structure = new Structure();
        $form = $this->createForm(
            StructureType::class,
            $structure
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($structure);
            $em->flush();
            $succes = 'La structure ' . $structure->getnom() . ' ';
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
     * @param Request $request
     * @param Structure $structure
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, Structure $structure)
    {
        $deleteForm = $this->createDeleteForm($structure);
        $form = $this->createForm(
            StructureType::class,
            $structure
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $succes = 'La structure ' . $structure->getnom() . ' ';
            $succes .= ' a bien été modifiée';
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
                'form' => $form->createView(),
                'delete_form' => $deleteForm->createView()
            )
        );
    }

    /**
     * Deletes a structure entity.
     * @param Request $request
     * @param Structure $structure
     * @return RedirectResponse
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
     * @return FormInterface
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

    /**
     * Envoie un mail aux parents de la structure
     * @param Request $request
     * @param Structure $structure - la structure concernée
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function envoyerMailAction(Request $request, Structure $structure = null)
    {
        $form = $this->createForm(
            MailType::class
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $documents = $form->getData()['documents'];
            array_pop($documents);

            if (!$structure) {
                $em = $this->getDoctrine()->getManager();
                $structures = $em
                    ->getRepository('BalancelleBundle:Structure')
                    ->findBy(array('active' => 1));
                foreach ($structures as $str) {
                    $this->get('communication')->envoyerMailStructure(
                        $str,
                        $form->getData()['sujet'],
                        $form->getData()['message'],
                        $documents
                    );
                }
            } else {
                $this->get('communication')->envoyerMailStructure(
                    $structure,
                    $form->getData()['sujet'],
                    $form->getData()['message'],
                    $documents
                );
            }
            $succes = 'Votre email a bien été envoyé aux parents';
            $this->addFlash('success', $succes);
            if ($structure !== null) {
                return $this->redirectToRoute(
                    'structure_edit',
                    array('id' => $structure->getId())
                );
            }

            return $this->redirectToRoute('structure_index',array());
        }
        $titre = 'Envoyer un email aux parents ';
        if ($structure !== null) {
            $titre .= 'de la structure ' . $structure->getNom();
        }

        return $this->render(
            '@Balancelle/Communication/email_create.html.twig',
            array(
                'form' => $form->createView(),
                'titre' => $titre
            )
        );
    }
}
