<?php

namespace BalancelleBundle\EventSubscribe;

use BalancelleBundle\Controller\MenuInterface;
use BalancelleBundle\Controller\UserPreferenceController;
use BalancelleBundle\Entity\Calendrier;
use BalancelleBundle\Entity\Enfant;
use BalancelleBundle\Entity\Famille;
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

    /**
     * @var
     */
    private $menu;

    /**
     * @var UserPreferenceController
     */
    private $userPreferenceController;

    /**
     * FamilleSubscriber constructor.
     * @param ContainerInterface $container
     * @param Security $security
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        ContainerInterface $container,
        Security $security,
        EntityManagerInterface $entityManager,
        UserPreferenceController $userPreferenceController
    ) {
        $this->session = $container->get('session');
        $this->security = $security;
        $this->entityManager = $entityManager;
        $this->menus = [];
        $this->userPreferenceController = $userPreferenceController;
    }

    /**
     * @param FilterControllerEvent $event
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
            && (!$this->session->has('menus')
                || ($this->session->has('rebootmenu')
                    && $this->session->get('rebootmenu')
                )
            )
        ) {
            $this->genererMenus();
            $this->session->set('menus', $this->menus);
            $this->session->set('rebootmenu', false);
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
        $admin = new MenuOuvrant('Administration', 'fa fa-desktop');
        $menu = new Menu(
            'admin_tableaudebord',
            'Tableau de bord ',
            'ti-dashboard'
        );
        $admin->addMenu($menu);
        $menu = new Menu(
            'admin_initialisernouvelleannee',
            'Initialiser une nouvelle année ',
            'ti-control-forward'
        );
        $admin->addMenu($menu);
        $this->menus[] = $admin;
        $this->menus[] = new Menu('user_index', 'Utilisateurs', 'fa fa-user');
        $this->menus[] = new Menu('famille_index', 'Familles', 'fa fa-group');
        $this->menus[] = new Menu(
            'structure_index',
            'Structures',
            'fa fa-sitemap'
        );
        $this->menus[] = new Menu(
            'calendrier_index',
            'Calendriers',
            'fa fa-calendar-check-o'
        );

        $courses = new MenuOuvrant('Courses', 'fa fa-shopping-cart');
        $menu = new Menu(
            'magasin_index',
            'Liste des magasins',
            'fa fa-university'
        );
        $courses->addMenu($menu);
        $menu = new Menu(
            'course_index',
            'Liste des courses',
            'fa fa-shopping-bag '
        );
        $courses->addMenu($menu);
        $this->menus[] = $courses;
    }

    private function genererMenuParent()
    {
        $this->menus[] = new Menu(
            'famille_tableauDeBord',
            'Ma famille',
            'ti-home'
        );

        $pref = $this->userPreferenceController->recupererLesPreferencesUtilisateur();
        if ($pref->getCovid()) {
            $this->menus[] = new Menu(
                'famille_liste',
                'Liste des familles',
                'ti-email'
            );
        }
    }

    /**
     * Génère le menu pour les la partie permanence
     * @return MenuOuvrant
     */
    private function menuPermanences()
    {
        $permanences = new MenuOuvrant('Permanences', 'ti-agenda');

        if ($this->security->isGranted('ROLE_ADMIN')) {
            $structures = $this->entityManager->getRepository(Structure::class)
                ->getStructuresAvecCalendrier();
            foreach ($structures as $structure) {
                $menu = new Menu(
                    'admin_permanence_structure',
                    'Permanence ' . $structure->getNom(),
                    'ti-layout-list-thumb-alt',
                    ['structure' => $structure->getNom()]
                );
                $permanences->addMenu($menu);
            }
        } elseif ($this->security->isGranted('ROLE_PARENT')) {
            $this->menuPermanenceFamille($permanences);
        }

        return $permanences;
    }

    /**
     * Génère le menu pour l'accès aux permanences en fonction de l'appatenance
     * des enfants aux différentes structures.
     * @param $famille
     * @return array
     */
    private function menuPermanenceFamille($permanences)
    {
        $familles = $this->session->get('familles');
        $menuPerm = [];
        $emFamille = $this->entityManager->getRepository(Famille::class);
        $emEnfant = $this->entityManager->getRepository(Enfant::class);
        $emStructure = $this->entityManager->getRepository(Structure::class);
        if ($familles !== null) {
            foreach ($familles as $famille) {
                $famille = $emFamille->findOneBy(['id' => $famille->getId()]);
                foreach ($famille->getEnfants() as $enfant) {
                    $enfant = $emEnfant->findOneBy(['id' => $enfant->getId()]);
                    $structure = $emStructure->findOneBy(
                        ['id' => $enfant->getStructure()->getId()]
                    );
                    if ($this->verifieSiStructureAunCalendrier(
                        $structure
                    ) &&
                        !in_array(
                            $structure->getNomCourt(),
                            $menuPerm,
                            true
                        )
                    ) {
                        $menuPerm[] = $structure->getNomCourt();
                        $menu = new Menu(
                            'permanence_index',
                            'Permanence ' . $structure->getNom(),
                            'ti-layout-list-thumb-alt',
                            ['structure' => $structure->getNom()]
                        );
                        $permanences->addMenu($menu);
                    }
                }
            }
        }

        return $permanences;
    }


    private function verifieSiStructureAunCalendrier($structure)
    {
        $calendriers = $this->entityManager->getRepository(Calendrier::class)
            ->findBy(['structure' => $structure, 'active' => 1]);
        return count($calendriers);
    }
}
