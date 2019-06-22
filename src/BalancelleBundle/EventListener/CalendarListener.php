<?php
/**
 * Created by IntelliJ IDEA.
 * User: fleuryt
 * Date: 24/02/19
 * Time: 21:17
 */

namespace BalancelleBundle\EventListener;

//use BalancelleBundle\Entity\Famille;
use BalancelleBundle\Entity\Permanence;
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
    //private $security;

    private $famille;

    public function __construct(
        EntityManagerInterface $em,
        UrlGeneratorInterface $router,
        ContainerInterface  $container,
        Security $security
    ) {
        $this->router = $router;
        $this->em = $em;
        //$this->security = $security;
        $this->famille = $container->get('session')->get('famille');
    }

    public function load(CalendarEvent $calendar)
    {
        $startDate = $calendar->getStart();
        $endDate = $calendar->getEnd();
        $filters = $calendar->getFilters();

        $permanences = $this->em->getRepository(Permanence::class)
            ->createQueryBuilder('b')
            ->andWhere('b.debut BETWEEN :startDate and :endDate')
            ->setParameter('startDate', $startDate->format('Y-m-d H:i:s'))
            ->setParameter('endDate', $endDate->format('Y-m-d H:i:s'))
            ->getQuery()->getResult();

        foreach($permanences as $permanence) {
            $permanenceEvent = $this->formatPermanence($permanence);
            $calendar->addEvent($permanenceEvent);
        }
    }

    /**
     * Format une permanence en fonction de la date et de sa disponibilité
     * @param Permanence $permanence - la permanence
     * @return Event
     * @throws Exception
     */
    private function formatPermanence(
        Permanence $permanence
    )
    {
        $permanenceEvent =new Event(
            $permanence->getDebut()->format('H:i') . ' '
            . $permanence->getTitre(),
            $permanence->getDebut(),
            $permanence->getFin()
        );
        $permanencePassee = new DateTime() > new DateTime($permanence->getDebut()->format('Y-m-d'));

        $backgroundColor = '#427fb0'; //couleur perm effectuée de la famille
        $borderColor = 'red';
        $url = null;
        $titre = $permanence->getDebut()->format('H:i');
        if($permanence->getFamille() !== null) {
            if (
                $this->famille &&
                ($permanence->getFamille()->getId() === $this->famille->getId())
            ) {
                if($permanencePassee) {
                    $backgroundColor = '#ae2305';
                    $borderColor = 'blue';
                    $titre .= ' Permanence réalisée';
                } else {
                    $titre .= ' Permanence a réaliser';
                }
            } else {
                $backgroundColor = '#252dd3';
                $borderColor = 'yellow';
                $titre = $permanence->getDebut()->format('H:i');
                $titre .= ' Permanence non disponible';
            }
        } else {
            $backgroundColor = $permanence->getCouleur();
            $borderColor = 'blue';

            if($permanencePassee) {
                $backgroundColor = '#7c7c70';
                $borderColor = 'black';
                $titre .= ' Permanence terminée';
            } else {
                $titre .= ' Permanence disponible';
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
        $permanenceEvent->setTitle($titre);
        if ($url !== null) {
            $permanenceEvent->addOption('url', $url);
        }

        return $permanenceEvent;
    }
}