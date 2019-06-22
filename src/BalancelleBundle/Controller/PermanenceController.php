<?php

namespace BalancelleBundle\Controller;

/*use BalancelleBundle\Entity\Permanence;
use phpDocumentor\Reflection\Types\Integer;*/
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Permanence controller.
 *
 */
class PermanenceController extends Controller
{
    /**
     * Accès au planning des permanences
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $famille = $this->get('session')->get('famille');
        return $this->render(
            '@Balancelle/Permanence/permanence.html.twig',
            array('famille' => $famille)
        );
    }


    /**
     * Récupère les dates d'une semaine (du lundi au samedi) en fonction du
     * numéro de semaine et de l'année.
     * @param int $numSemaine - le numéro de la semaine
     * @param int $annee - l'année
     * @param string $format - le formatage de retour pour la date
     * @return \Symfony\Component\HttpFoundation\Response
     */
    /*public function recupererLesJoursDeLaSemaine(
        int $numSemaine,
        int$annee,
        $format="d/m/Y"
    ) {
        $tabRetour = null;
        $firstDayInYear=date("N",mktime(0,0,0,1,1,$annee));
        $shift=(8-$firstDayInYear)*86400;
        if ($firstDayInYear<5){
            $shift=-($firstDayInYear-1)*86400;
        }
        $weekInSeconds=0;
        if ($numSemaine>1) {
            $weekInSeconds=($numSemaine-1)*604800;
        }
        for($i = 1; $i<6; $i++) {
            $timestamp=mktime(0,0,0,1,$i,$annee)+$weekInSeconds+$shift;
            $tabRetour[] = date($format,$timestamp);
        }

        return $tabRetour;
    }

    /**
     * Creates a new permanence entity.
     *
     */
    /*public function newAction(Request $request)
    {
        $permanence = new Permanence();
        $form = $this->createForm('BalancelleBundle\Form\PermanenceType', $permanence);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($permanence);
            $em->flush();

            return $this->redirectToRoute('permanence_show', array('id' => $permanence->getId()));
        }

        return $this->render('permanence/new.html.twig', array(
            'permanence' => $permanence,
            'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing permanence entity.
     *
     */
    /*public function editAction(Request $request, $id )/* Permanence $permanence)*/
    /*{
        $utilisateur = $this->getUser()->getPrenom() . " ";
        $utilisateur .= $this->getUser()->getNom();

        $em = $this->getDoctrine()->getManager();

        $famille = $em
            ->getRepository('BalancelleBundle:Famille')
            ->getFamilleByIdParent($this->getUser()->getId());


        $permanence = $em
            ->getRepository('BalancelleBundle:Permanence')
            ->find($id);

        $editForm = $this->createForm('BalancelleBundle\Form\PermanenceType', $permanence);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('permanence_edit', array('id' => $permanence->getId()));
        }


        return $this->render('@Balancelle/Permanence/edit.html.twig', array(
            'permanence' => $permanence,
            'edit_form' => $editForm->createView(),
            'utilisateur' => $utilisateur,
            'famille' => $famille
        ));
    }

    /**
     * Deletes a permanence entity.
     *
     */
    /*public function deleteAction(Request $request, Permanence $permanence)
    {
        $form = $this->createDeleteForm($permanence);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($permanence);
            $em->flush();
        }

        return $this->redirectToRoute('permanence_index');
    }

    /**
     * Creates a form to delete a permanence entity.
     *
     * @param Permanence $permanence The permanence entity
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    /*private function createDeleteForm(Permanence $permanence)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('permanence_delete', array('id' => $permanence->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * Enregistre l'inscription d'une famille à une permanence
     * @param Request $request
     * @param $id - l'id de la permanence
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function inscriptionAction(Request $request, $id )
    {
        $em = $this->getDoctrine()->getManager();
        $famille = $em
            ->getRepository('BalancelleBundle:Famille')
            ->findByFamille($this->getUser()->getId());
        $permanence = $em
            ->getRepository('BalancelleBundle:Permanence')
            ->find($id);

        $editForm = $this->createForm(
            'BalancelleBundle\Form\PermanenceType',
            $permanence
        );
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $permanence->setFamille($famille);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute(
                'permanence_index',
                array('id' => $permanence->getId())
            );
        }

        return $this->render(
            '@Balancelle/Permanence/inscription.html.twig',
            array(
                'permanence' => $permanence,
                'inscription_form' => $editForm->createView(),
                'famille' => $famille
            )
        );
    }
}
