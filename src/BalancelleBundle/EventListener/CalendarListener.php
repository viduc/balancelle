<?php
/**
 * Created by IntelliJ IDEA.
 * User: fleuryt
 * Date: 24/02/19
 * Time: 21:17
 */

namespace BalancelleBundle\EventListener;

use BalancelleBundle\Entity\Calendrier;
use BalancelleBundle\Entity\Permanence;
use BalancelleBundle\Entity\Semaine;
use DateTime;
use Exception;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;
use Doctrine\ORM\EntityManagerInterface;
use CalendarBundle\Entity\Event;
use CalendarBundle\Event\CalendarEvent;

class CalendarListener
{
    /**
     * @var UrlGeneratorInterface
     */
    private $router;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var Session
     */
    //private $session;

    /**
     * @var Security
     */
    private $security;

    /**
     * @var mixed
     */
    private $familles;

    /**
     * @var String
     */
    private $structure;

    private $familleIds;

    public function __construct(
        EntityManagerInterface $em,
        UrlGeneratorInterface $router,
        ContainerInterface  $container,
        Security $security
    ) {
        $this->router = $router;
        $this->em = $em;
        $this->security = $security;
        $this->familles = $container->get('session')->get('familles');
        $this->familleIds = [];
        foreach ($this->familles as $famille) {
            $this->familleIds[] = $famille->getId();
        }
        $this->structure = $container->get('session')->get('structure');
    }

    public function load(CalendarEvent $calendar)
    {
        $startDate = $calendar->getStart();
        $endDate = $calendar->getEnd();
        $filters = $calendar->getFilters();

        $permanences = $this->em->getRepository(Permanence::class)
            ->createQueryBuilder('b')
            ->from(Calendrier::class, 'c')
            ->from(Semaine::class, 's')
            ->andWhere('b.debut BETWEEN :startDate and :endDate')
            ->andWhere('c.structure = :structure')
            ->andWhere('s.calendrier = c.id')
            ->andWhere('b.semaine = s.id')
            ->andWhere('c.active = 1')
            ->setParameter('startDate', $startDate->format('Y-m-d H:i:s'))
            ->setParameter('endDate', $endDate->format('Y-m-d H:i:s'))
            ->setParameter('structure', $this->structure)
            ->getQuery()->getResult();

        $tabPermanencesJour = [];
        foreach ($permanences as $permanence) {
            try {
                $permanenceEvent = $this->formatPermanence($permanence);
                $calendar->addEvent($permanenceEvent);
                $tabPermanencesJour[$permanence->getDebut()->format('Y-m-d')] =
                    $permanence;
            } catch (Exception $e) {
            }
        }
        if ($this->security->isGranted('ROLE_ADMIN')) {
            try {
                $this->genererPermanenceAjouter($tabPermanencesJour, $calendar);
            } catch (Exception $e) {
            }
        }
    }

    /**
     * Format une permanence en fonction de la date et de sa disponibilité
     * @param Permanence $permanence - la permanence
     * @return Event
     * @throws Exception
     */
    private function formatPermanence(Permanence $permanence): Event
    {
        $titre = $permanence->getDebut()->format('H:i');
        $titre .= ' ' . $permanence->getTitre();
        $permanenceEvent =new Event(
            $titre,
            $permanence->getDebut(),
            $permanence->getFin()
        );
        $permanencePassee = new DateTime() > new DateTime(
            $permanence->getDebut()->format('Y-m-d')
        );

        $backgroundColor = '#427fb0'; //couleur perm effectuée de la famille
        $borderColor = 'red';
        $url = null;
        $titre = $permanence->getDebut()->format('H:i');
        if ($permanence->getFamille() !== null) {
            if ($this->security->isGranted('ROLE_ADMIN')) {
                $titre .= ' ' . $permanence->getFamille()->getNom();
                if ($permanence->getEchange()) {
                    $titre .= ' !! ECHANGE !!';
                    $backgroundColor = '#DC143C';
                }
            } elseif (// la permanence est attribuée à la famille connectée
                $this->familles &&
                in_array($permanence->getFamille()->getId(), $this->familleIds, true)
            ) {
                if ($permanencePassee) {
                    $backgroundColor = '#ae2305';
                    $borderColor = 'blue';
                    $titre .= ' Réalisée';
                } else {
                    $titre .= ' A faire';
                }
                $url = $this->router->generate(
                    'permanence_inscription',
                    array('id' => $permanence->getId())
                );
            } elseif ($permanence->getEchange()) {
                $backgroundColor = '#bd5d09';
                $borderColor = 'green';
                $titre = $permanence->getDebut()->format('H:i');
                $titre .= " Proposée à l'échange";
                $url = $this->router->generate(
                    'permanence_inscription',
                    array('id' => $permanence->getId())
                );
            } else {
                $backgroundColor = '#6b70e3';
                $borderColor = 'yellow';
                $titre = $permanence->getDebut()->format('H:i');
                $titre .= ' Indisponible';
            }
        } else {
            $backgroundColor = $permanence->getCouleur();
            $borderColor = 'blue';

            if ($permanencePassee) {
                $backgroundColor = '#7c7c70';
                $borderColor = 'black';
                $titre .= ' Terminée';
            } else {
                $titre .= ' Disponible';
                $url = $this->router->generate(
                    'permanence_inscription',
                    array('id' => $permanence->getId())
                );
            }
        }

        $permanenceEvent->setOptions(
            [
                'backgroundColor' => $backgroundColor,
                'borderColor' => $borderColor
            ]
        );
        if ($this->security->isGranted('ROLE_ADMIN')) {
            $url = $this->router->generate(
                'permanence_inscription',
                array('id' => $permanence->getId())
            );
        }
        $permanenceEvent->setTitle($titre);
        if ($url !== null) {
            $permanenceEvent->addOption('url', $url);
        }

        return $permanenceEvent;
    }

    /**
     * Génère le bouton pour ajouter une permanence à chaque jour des calendriers
     * @param $tabPerm
     * @param CalendarEvent $calendar
     */
    private function genererPermanenceAjouter($tabPerm, $calendar)
    {
        foreach ($tabPerm as $permanence) {
            $permanenceEvent = new Event(
                'Ajouter une permanence',
                $permanence->getDebut(),
                $permanence->getFin()
            );
            $permanenceEvent->setOptions(
                [
                    'backgroundColor' => '#dc6c0f',
                    'borderColor' => '#2748da'
                ]
            );
            $url = $this->router->generate(
                'admin_permanence_creer',
                array(
                    'semaineId' => $permanence->getSemaine()->getId(),
                    'date' => $permanence->getDebut()->format('Y-m-d')
                )
            );
            $permanenceEvent->addOption('url', $url);
            $calendar->addEvent(
                $permanenceEvent
            );
        }
    }
}
