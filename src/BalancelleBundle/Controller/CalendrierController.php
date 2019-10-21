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
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use BalancelleBundle\Form\CalendrierType;

/**
 * Calendrier controller.
 *
 */
class CalendrierController extends Controller
{
    /**
     * Liste tout les calendriers
     * @return Response
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
     * @return RedirectResponse|Response
     */
    public function newAction(Request $request)
    {
        $calendrier = new Calendrier();
        $form = $this->createForm(
            CalendrierType::class,
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

            try {
                $this->genererLesSemaines(
                    $calendrier//,
                    //$calendrier->getDateDebut(),
                    //$calendrier->getDateFin()
                );
            } catch (Exception $e) { //TODO gérer l'erreur ici
                $this->addFlash('success', 'blop');
            }
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
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, Calendrier $calendrier)
    {
        $deleteForm = $this->createDeleteForm($calendrier);
        $editForm = $this->createForm(
            CalendrierType::class,
            $calendrier
        );
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $calendrier->setAnneeDebut($calendrier->getDateDebut()->format('Y'));
            $calendrier->setAnneeFin($calendrier->getDateFin()->format('Y'));
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
     * @param Request $request
     * @param Calendrier $calendrier
     * @return RedirectResponse
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
     * @param Calendrier $calendrier
     * @return FormInterface
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
        Calendrier $calendrier/*,
        $dateDebut,
        $dateFin*/
    ) {
        $em = $this->getDoctrine()->getManager();
        $numPremiereSemaine = date(
            'W',
            strtotime($calendrier->getDateDebut()->format('Y-m-d'))
        );
        $numDerniereSemaine = date(
            'W',
            strtotime($calendrier->getDateFin()->format('Y-m-d'))
        );

        if ($calendrier->getAnneeDebut() === $calendrier->getAnneeFin()) {

            for ($i=$numPremiereSemaine; $i<=$numDerniereSemaine; $i++) {
                $this->creerSemaine($i, $calendrier, true);
            }
        } else {
            for ($i=$numPremiereSemaine; $i<=52; $i++) {
                $this->creerSemaine($i, $calendrier, true);
            }
            for ($i=1; $i<=$numDerniereSemaine; $i++) {
                $this->creerSemaine($i, $calendrier, false);
            }
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
     * Créer une semaine et génère les permanences
     * @param int $numSemaine
     * @param Calendrier $calendrier
     * @throws Exception
     */
    private function creerSemaine($numSemaine, Calendrier $calendrier, $debut)
    {
        $dates = $this->getStartAndEndDate(
            $numSemaine, $calendrier->getDateDebut()->format('Y')
        );
        $semaine = new Semaine();
        $semaine->setNumero($numSemaine);
        $semaine->setAnnee($calendrier->getDateDebut()->format('Y'));
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
        $em = $this->getDoctrine()->getManager();
        $em->persist($semaine);
        $em->flush();
        $this->genererLesPermanences($calendrier, $semaine, $debut);
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
            $datePerm = $dtoDebut->format('Y-m-d');
            if (
                $datePerm <= $calendrier->getDateFin()->format('Y-m-d') &&
                $datePerm >= $calendrier->getDateDebut()->format('Y-m-d')
            ) {
                /* génération des permanences du matin */
                for ($j = 0; $j < $calendrier->getNbrPermanenceMatin(); $j++) {
                    $permanence = new Permanence();
                    $permanence->setTitre('Permanence matin');
                    $permanence->setCommentaire('Ici commentaire');
                    $permanence->setActive(true);
                    $permanence->setDebut($dtoDebut->setTime(8, 30));
                    $permanence->setFin($dtoFin->setTime(11, 30));
                    $permanence->setSemaine($semaine);
                    $permanence->setCouleur('#567c3f');
                    $em->persist($permanence);
                }

                /* génération des permanences de l'am */
                for ($j = 0; $j < $calendrier->getNbrPermanenceAM(); $j++) {
                    $permanence = new Permanence();
                    $permanence->setTitre('Permanence AM');
                    $permanence->setCommentaire('Ici commentaire');
                    $permanence->setActive(true);
                    $permanence->setDebut($dtoDebut->setTime(14, 00));
                    $permanence->setFin($dtoFin->setTime(17, 30));
                    $permanence->setSemaine($semaine);
                    $permanence->setCouleur('#635178');
                    $em->persist($permanence);
                    //$em->flush();
                }
                $this->addFlash('success', 'ici');
            }
            $em->flush();
        }
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
    private function getStartAndEndDate($week, $year)
    {
        $debut = new DateTime();
        $fin = new DateTime();
        $ret['week_start'] = $debut->setISODate($year, $week);
        $ret['week_end'] = $fin->setISODate($year, $week, 7);
        return $ret;
    }
}
