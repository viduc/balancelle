<?php

namespace BalancelleBundle\Controller;

use BalancelleBundle\Entity\Calendrier;
use BalancelleBundle\Entity\Permanence;
use BalancelleBundle\Entity\Semaine;
use BalancelleBundle\Entity\Structure;
use DateTime;
use Exception;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Calendrier controller.
 *
 */
class CalendrierController extends Controller
{
    /**
     * Liste tout les calendriers
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $calendriers = $em->getRepository('BalancelleBundle:Calendrier')->findAll();

        return $this->render('@Balancelle/Calendrier/index.html.twig', array(
            'calendriers' => $calendriers,
        ));
    }

    /**
     * Créer un calendrier
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $calendrier = new Calendrier();
        $form = $this->createForm(
            'BalancelleBundle\Form\CalendrierType',
            $calendrier
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $calendrier->setAnneeDebut($calendrier->getDateDebut()->format('Y'));
            $calendrier->setAnneeFin($calendrier->getDateFin()->format('Y'));
            $em = $this->getDoctrine()->getManager();
            $em->persist($calendrier);
            $em->flush();
            $succes = 'Le calendrier  ';
            $succes .= ' a bien été enregistré';
            $this->addFlash('success', $succes);

            $this->genererLesSemaines(
                $calendrier,
                $calendrier->getDateDebut(),
                $calendrier->getDateFin()
            );
            return $this->redirectToRoute(
                'calendrier_edit',
                array('id' => $calendrier->getId())
            );
        }

        return $this->render(
            '@Balancelle/Calendrier/edit.html.twig',
            array(
                'calendrier' => $calendrier,
                'form' => $form->createView(),
            )
        );
    }

    /**
     * Modification d'un calendrier
     * @param Request $request
     * @param Calendrier $calendrier
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Calendrier $calendrier)
    {
        $deleteForm = $this->createDeleteForm($calendrier);
        $editForm = $this->createForm(
            'BalancelleBundle\Form\CalendrierType',
            $calendrier
        );
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $calendrier->setAnneeDebut($calendrier->getDateDebut()->format("Y"));
            $calendrier->setAnneeFin($calendrier->getDateFin()->format("Y"));
            $this->getDoctrine()->getManager()->flush();
            $succes = 'Le calendrier ';
            $succes .= ' a bien été modifié';
            $this->addFlash('success', $succes);
            return $this->redirectToRoute(
                'calendrier_edit',
                array('id' => $calendrier->getId())
            );
        }

        return $this->render(
            '@Balancelle/Calendrier/edit.html.twig',
            array(
                'calendrier' => $calendrier,
                'form' => $editForm->createView(),
                'delete_form' => $deleteForm->createView()
            )
        );
    }

    /**
     * Deletes a structure entity.
     *
     */
    public function deleteAction(Request $request, Calendrier $calendrier)
    {
        $form = $this->createDeleteForm($calendrier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($calendrier);
            $em->flush();
            $succes = 'Le calendrier ';
            $succes .= ' a bien été supprimé';
            $this->addFlash('success', $succes);
        }

        return $this->redirectToRoute('calendrier_index');
    }

    /**
     * Creates a form to delete a structure entity.
     *
     * @param Structure $structure The structure entity
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createDeleteForm(Calendrier $calendrier)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl(
                'calendrier_delete',
                array('id' => $calendrier->getId())
            ))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * Génère tout les objets semaines pour un calendrier
     * @param Calendrier $calendrier - le calendrier
     * @param string $dateDebut - la date de début de génération
     * @param string $dateFin - la date de fin de génération
     * @return bool
     * @throws Exception
     */
    private function genererLesSemaines(
        Calendrier $calendrier,
        $dateDebut,
        $dateFin
    ) {
        $em = $this->getDoctrine()->getManager();
        $numPremiereSemaine = date(
            'W',
            strtotime($dateDebut->format('Y-m-d'))
        );
        $numDerniereSemaine = date(
            'W',
            strtotime($dateFin->format('Y-m-d'))
        );

        for ($i=$numPremiereSemaine; $i<=52; $i++){
            $dates = $this->getStartAndEndDate($i, $dateDebut->format('Y'));
            $semaine = new Semaine();
            $semaine->setNumero($i);
            $semaine->setAnnee($dateDebut->format('Y'));
            $semaine->setDateDebut($dates['week_start']);
            $semaine->setDateFin($dates['week_end']);
            $semaine->setCommentaire('Generée par calendrier');
            $semaine->setNbrPermanenceMatin(
                $calendrier->getNbrPermanenceMatin()
            );
            $semaine->setNbrPermanenceAM(
                $calendrier->getNbrPermanenceAM()
            );
            $semaine->setCalendrier($calendrier);
            $em->persist($semaine);
            $this->genererLesPermanences($calendrier, $semaine, true);
        }

        for ($i=1; $i<=$numDerniereSemaine; $i++){
            $semaine = new Semaine();
            $semaine->setNumero($i);
            $semaine->setAnnee($dateDebut->format("Y"));
            $semaine->setCommentaire('Generée par calendrier');
            $semaine->setNbrPermanenceMatin(
                $calendrier->getNbrPermanenceMatin()
            );
            $semaine->setNbrPermanenceAM(
                $calendrier->getNbrPermanenceAM()
            );
            $semaine->setCalendrier($calendrier);
            $em->persist($semaine);
        }

        $em->flush();
        $succes = 'Les semaines du calendrier  ';
        $succes .= ' ont bien été enregistrées';
        $this->addFlash('success', $succes);
        $succes = 'Les permanences du calendrier  ';
        $succes .= ' ont bien été enregistrées';
        $this->addFlash('success', $succes);
        return true;
    }

    /**
     * Permet de générer les dates de début et de fin d'une semaine en fonction
     * de son numéro et de l'année
     * @param int $week - le numéro de la semaine
     * @param int $year - l'année
     * @return array
     * @throws Exception
     */
    function getStartAndEndDate($week, $year) {
        $debut = new DateTime();
        $fin = new DateTime();
        $ret['week_start'] = $debut->setISODate($year, $week);
        $ret['week_end'] = $fin->setISODate($year, $week, 7);
        return $ret;
    }

    /**
     * genererLesPermanences
     * @param Calendrier $calendrier - le calendrier
     * @param Semaine $semaine - la semaine
     * @param boolean $debut - true si debut année
     * @return bool
     * @throws Exception
     */
    private function genererLesPermanences(
        Calendrier $calendrier,
        semaine $semaine,
        $debut
    )
    {
        $em = $this->getDoctrine()->getManager();
        $dtoDebut = new DateTime();
        $dtoFin = new DateTime();
        if ($debut) {
            $dtoDebut->setISODate(
                $calendrier->getAnneeDebut(),
                $semaine->getNumero()
            );
            $dtoFin->setISODate(
                $calendrier->getAnneeDebut(),
                $semaine->getNumero()
            );
        } else {
            $dtoDebut->setISODate(
                $calendrier->getAnneeFin(),
                $semaine->getNumero()
            );
            $dtoFin->setISODate(
                $calendrier->getAnneeFin(),
                $semaine->getNumero()
            );
        }

        for ($i=0; $i<5; $i++) {
            if ($i !== 0) {
                $dtoDebut->modify(
                    '+1 days'
                );
                $dtoFin->modify(
                    '+1 days'
                );
            }
            /* génération des permanences du matin */
            for ($j=0; $j<$calendrier->getNbrPermanenceMatin(); $j++) {
                $permanence = new Permanence();
                $permanence->setTitre('Permanence matin');
                $permanence->setCommentaire('Ici commentaire');
                $permanence->setActive(true);
                $permanence->setDebut($dtoDebut->setTime(8, 30));
                $permanence->setFin($dtoFin->setTime(11, 30));
                $permanence->setSemaine($semaine);
                $permanence->setCouleur("#567c3f");
                $em->persist($permanence);
                $em->flush();
            }

            /* génération des permanences de l'am */
            for ($j=0; $j<$calendrier->getNbrPermanenceAM(); $j++) {
                $permanence = new Permanence();
                $permanence->setTitre('Permanence AM');
                $permanence->setCommentaire('Ici commentaire');
                $permanence->setActive(true);
                $permanence->setDebut($dtoDebut->setTime(14, 00));
                $permanence->setFin($dtoFin->setTime(17, 30));
                $permanence->setSemaine($semaine);
                $permanence->setCouleur("#635178");
                $em->persist($permanence);
                $em->flush();
            }
        }
        return true;
    }
}
