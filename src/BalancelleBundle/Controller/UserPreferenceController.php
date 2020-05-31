<?php

namespace BalancelleBundle\Controller;

use BalancelleBundle\Entity\Preference;
use BalancelleBundle\Form\PreferenceType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
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

    /**
     * @var SessionInterface
     */
    private $session;

    public function __construct(
        Security $security,
        EntityManagerInterface $entityManager,
        SessionInterface $session
    ) {
        $this->security = $security;
        $this->em = $entityManager;
        $this->session = $session;
    }

    /**
     * Liste toutes les preferences utilisateur
     * @param Request $request
     * @return Response
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function indexAction(Request $request)
    {
        $user = $this->security->getUser();
        $form = $this->createForm(PreferenceType::class, $user->getPreference());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->em->flush();
                $succes = 'Vos préférences ont bien été modifées';
                $this->addFlash('success', $succes);
                $this->session->set('rebootmenu', true);

                return $this->redirectToRoute('preference_index');
            } catch (OptimisticLockException $e) {
            } catch (ORMException $e) {
                $error = 'Une erreur interne n\'a pas permis l\'enregistrement';
                $error .= ' de vos préférences';
                $this->addFlash('error', $error);
            }
        }

        return $this->render(
            '@Balancelle/UserPreference/index.html.twig',
            array(
                'preferences' => $this->recupererLesPreferencesUtilisateur(),
                'form' => $form->createView()
            )
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
