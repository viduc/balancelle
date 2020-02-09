<?php

namespace BalancelleBundle\EventSubscribe;

use BalancelleBundle\Controller\MenuInterface;
use BalancelleBundle\Entity\Menu;
use BalancelleBundle\Entity\MenuOuvrant;
use BalancelleBundle\Entity\Structure;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Security;

class MenuSubscriber implements EventSubscriberInterface
{
    /**
     * @var object|Session
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

    private $menu;

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
        $this->menus = [];
    }

    /**
     * @param FilterControllerEvent $event
     * @throws NonUniqueResultException
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

        if (($controller[0] instanceof MenuInterface)
            && !$this->session->has('menus')) {
            $this->genererMenus();
            $this->session->set('menus', $this->menus);
        }
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }

    /**
     * Génère les menus pour l'utilisateur connecté
     */
    private function genererMenus()
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            $this->genererMenuAdmin();
        }
        if ($this->security->isGranted('ROLE_PARENT')) {
            $this->genererMenuParent();
        }
        $this->menus[] = $this->menuPermanences();
    }

    /**
     * Génère le menu spécifique pour les administrateurs
     */
    private function genererMenuAdmin()
    {
        $this->menus[] = new Menu('user_index', 'Utilisateurs', 'fa fa-user');
        $this->menus[] = new Menu('famille_index', 'Familles', 'fa fa-group');
        $this->menus[] = new Menu('structure_index', 'Structures', 'fa fa-sitemap');
        $this->menus[] = new Menu(
            'calendrier_index',
            'Calendriers',
            'fa fa-calendar-check-o'
        );
    }

    private function genererMenuParent()
    {
        $this->menus[] = new Menu('famille_tableauDeBord', 'Ma famille', 'ti-home');
    }

    /**
     * Génère le menu pour les la partie permanence
     * @return MenuOuvrant
     */
    private function menuPermanences()
    {
        $permanences = new MenuOuvrant('Permanences', 'ti-agenda');

        if ($this->security->isGranted('ROLE_ADMIN')) {
            $menu = new Menu(
                'admin_permanence_index',
                'Tableau de bord ',
                'ti-dashboard'
            );
            $permanences->addMenu($menu);
            $structures = $this->entityManager->getRepository(Structure::class)
                ->getStructuresAvecCalendrier();
            foreach ($structures as $structure) {
                $menu = new Menu(
                    'admin_permanence_structure',
                    'Permanence ' . $structure->getNom(),
                    'ti-layout-list-thumb-alt',
                    ['structure' => $structure->getNom()]);
                $permanences->addMenu($menu);
            }
        }

        return $permanences;
    }

    /**
     * Génère le menu pour l'accès aux permanences en fonction de l'appatenance
     * des enfants aux différentes structures.
     * @param $famille
     * @return array
     */
    /*private function menuPermanence($familles)
    {
        $menuPerm = [];
        if ($familles !== null) {
            foreach ($familles as $famille) {
                foreach ($famille->getEnfants() as $enfant) {
                    if (
                        $this->verifieSiStructureAunCalendrier(
                            $enfant->getStructure()
                        ) &&
                        !in_array(
                            $enfant->getStructure()->getNomCourt(),
                            $menuPerm,
                            true
                        )
                    ) {
                        $menuPerm[] = $enfant->getStructure()->getNomCourt();
                    }
                }
            }
        } else {
            $listeStructures = $this->entityManager->getRepository(
                Structure::class
            )->findBy(['active' => 1]);
            foreach ($listeStructures as $structure) {
                if ($this->verifieSiStructureAunCalendrier($structure)) {
                    $menuPerm[] = $structure->getNomCourt();
                }
            }
        }

        return $menuPerm;
    }


    private function verifieSiStructureAunCalendrier($structure)
    {
        $calendriers = $this->entityManager->getRepository(Calendrier::class)
            ->findBy(['structure' => $structure, 'active' => 1]);

        return count($calendriers);
    }*/
}
