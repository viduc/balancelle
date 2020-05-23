<?php

namespace BalancelleBundle\Controller;

use BalancelleBundle\Entity\Preference;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;

/**
 * UserPreference controller.
 *
 */
class UserPreferenceController extends AppController implements FamilleInterface
{
    /**
     * @var Security
     */
    private $security;

    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(
        Security $security,
        EntityManagerInterface $entityManager
    ) {
        $this->security = $security;
        $this->em = $entityManager;
    }

    /**
     * Liste toutes les preferences utilisateur
     * @return Response
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function indexAction()
    {
        return $this->render(
            '@Balancelle/UserPreference/index.html.twig',
            array('preferences' => $this->recupererLesPreferencesUtilisateur())
        );
    }

    /**
     * @return mixed
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function recupererLesPreferencesUtilisateur()
    {
        if ($this->security->getUser()->getPreference() === null) {
            $preference = new Preference();
            $preference->setCovid(false);
            $preference->setActive(true);
            $this->security->getUser()->setPreference($preference);
            $this->em->persist($this->security->getUser());
            $this->em->flush();
        }

        return $this->security->getUser()->getPreference();
    }

}
