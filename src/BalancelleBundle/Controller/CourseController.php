<?php

namespace BalancelleBundle\Controller;

use BalancelleBundle\Entity\Course;
use BalancelleBundle\Form\CourseType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CourseController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $courses = $em->getRepository('BalancelleBundle:Course')->findAll();
        return $this->render(
            '@Balancelle/Course/index.html.twig',
            array('courses' => $courses)
        );
    }

    /**
     * Modification d'une course
     * @param Request $request
     * @param Magasin $course
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, Course $course)
    {
        $deleteForm = $this->createDeleteForm($course);
        $editForm = $this->createForm(
            CourseType::class,
            $course
        );
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $succes = 'La course ';
            $succes .= ' a bien été modifiée';
            $this->addFlash('success', $succes);
            return $this->redirectToRoute(
                'course_edit',
                array('id' => $course->getId())
            );
        }

        return $this->render(
            '@Balancelle/Course/edit.html.twig',
            array(
                'course' => $course,
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
        $course = new Course();
        $form = $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($course);
            $em->flush();
            $succes = 'La course  ';
            $succes .= ' a bien été enregistrée';
            $this->addFlash('success', $succes);

            return $this->redirectToRoute(
                'course_edit',
                array('id' => $course->getId())
            );
        }

        return $this->render(
            '@Balancelle/Course/edit.html.twig',
            array(
                'course' => $course,
                'form' => $form->createView()
            )
        );
    }

    /**
     * Creates a form to delete a structure entity.
     *
     * @param Course $course The structure entity
     *
     * @return FormInterface
     */
    private function createDeleteForm(Course $course)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl(
                'course_delete',
            array('id' => $course->getId())
            ))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }

    /**+
     * Deletes a structure entity.
     * @param Request $request
     * @param Course $course
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, Course $course)
    {
        $form = $this->createDeleteForm($course);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($course);
            $em->flush();
            $succes = 'La course ';
            $succes .= ' a bien été supprimée';
            $this->addFlash('success', $succes);
        }

        return $this->redirectToRoute('course_index');
    }
}
