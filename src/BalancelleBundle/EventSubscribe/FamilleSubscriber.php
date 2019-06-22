<?php
/**
 * Created by IntelliJ IDEA.
 * User: fleuryt
 * Date: 24/03/19
 * Time: 20:34
 */

namespace BalancelleBundle\EventSubscribe;

use BalancelleBundle\Controller\FamilleInterface;
use BalancelleBundle\Entity\Famille;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Security;

class FamilleSubscriber implements EventSubscriberInterface
{
    /**
     * @var object|\Symfony\Component\HttpFoundation\Session\Session
     */
    private $session;

    /**
     * @var Security
     */
    private $security;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * FamilleSubscriber constructor.
     * @param ContainerInterface $container
     * @param Security $security
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        ContainerInterface $container,
        Security $security,
        EntityManagerInterface $entityManager
    ) {
        $this->session = $container->get('session');
        $this->security = $security;
        $this->entityManager = $entityManager;
    }

    /**
     * @param FilterControllerEvent $event
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();
        /*
         * $controller passed can be either a class or a Closure.
         * This is not usual in Symfony but it may happen.
         * If it is a class, it comes in array format
         */
        if (!is_array($controller)) {
            return;
        }

        if ($controller[0] instanceof FamilleInterface) {
            if (!$this->session->has('famille')) {
                $this->session->set('famille', $this->getFamille());
            }
        }
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * ['eventName' => 'methodName']
     *  * ['eventName' => ['methodName', $priority]]
     *  * ['eventName' => [['methodName1', $priority], ['methodName2']]]
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }

    /**
     * Récupère la famille associée à l'utilisateur connecté
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getFamille()
    {
        return $this->entityManager->getRepository(Famille::class)
            ->createQueryBuilder('b')
            ->Where('b.parent1 = :user')
            ->orWhere('b.parent2 = :user')
            ->setParameter('user', $this->security->getUser())
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}