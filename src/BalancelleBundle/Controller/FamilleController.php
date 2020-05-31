<?php

namespace BalancelleBundle\Controller;

use BalancelleBundle\Entity\Course;
use BalancelleBundle\Entity\Enfant;
use BalancelleBundle\Entity\Famille;
use BalancelleBundle\Entity\Permanence;
use BalancelleBundle\Entity\User;
use BalancelleBundle\Form\MailCovidType;
use BalancelleBundle\Form\MailType;
use DateTime;
use Exception;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use BalancelleBundle\Form\FamilleType;

/**
 * Famille controller.
 *
 */
class FamilleController extends AppController implements FamilleInterface
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Permet de récupérer toutes les finformations des permanences pour une
     * famille
     * @param Famille $famille - la famille
     * @return array|null - un tableau contenant les informations
     */
   /* private function getInfoPermanenceFamille(Famille $famille)
    {
        $permanence = null;
        /** @var Famille $famille */
    /*    if ($famille) {
            $repository = $this->getDoctrine()->getRepository(Permanence::class);
            $permanence['faite'] = $repository->recupererLesPermanencesRealisees(
                $famille
            );
            $permanence['aFaire'] = $famille->getNombrePermanence();
            $permanence['inscrit'] = $repository->findByFamille($famille);
            $permanence['pourcentage'] = 0;
            if ($permanence['aFaire']) {
                $permanence['pourcentage'] =
                    count($permanence['faite'])*100/$permanence['aFaire'];
            }
            if ($permanence['pourcentage']>100) {
                $permanence['pourcentage'] = 100;
            }
        }
        return $permanence;
    }*/

    /**
     * Tableau de bord pour les familles
     * @return Response
     */
    public function tableauDeBordAction()
    {
        $familles = $this->get('session')->get('familles');

        if (!$familles) {
            return $this->redirectToRoute(
                'famille_erreurFamille',
                array()
            );
        }
        $id = 0;
        $repositoryPermanence = $this->em->getRepository(Permanence::class);
        $repositoryCourse = $this->em->getRepository(Course::class);

        $repository = $this->em->getRepository(Famille::class);
        foreach ($familles as $famille) {
            $famille = $repository->findOneBy(['id' =>$famille->getId()]);
            $tableauFamilles[$id]['famille'] = $famille;
            $tableauFamilles[$id]['permanences'] =
                $repositoryPermanence->getInformationPermanenceFamille($famille);
            $tableauFamilles[$id]['listePermanence'] =
                $repositoryPermanence->formaterListePermanence(
                    $tableauFamilles[$id]['permanences']
                );
            $tableauFamilles[$id]['courses'] = $repositoryCourse
                ->recupererLesCoursesDuneFamille($famille);
            $id++;
        }

        return $this->render(
            '@Balancelle/famille/tableauDeBord.html.twig',
            array('familles' => $tableauFamilles)
        );
    }

    public function erreurFamilleAction()
    {
        $this->addFlash('error', "Vous n'êtes pas inscrit dans une famille");
        return $this->render(
            '@Balancelle/famille/erreur_famille.html.twig',
            array()
        );
    }

    public function listeDesFamillesAction()
    {
        if ($this->getUser()->getPreference()->getCovid()) {
            $users = $this->em->getRepository(
                User::class
            )->recupererLesUtilisateursCovid();
            foreach ($users as $user) {
                $user->famille = $this->em->getRepository(
                    Famille::class
                )->findByFamille($user->getId());
                $user->famille->getEnfants();
            }
            return $this->render(
                '@Balancelle/famille/liste_des_familles.html.twig',
                array('users' => $users)
            );
        }

        $error = 'Vous ne pouvez pas accéder à cette page si vous n\'activez le paartage de votre adresse mail';
        $this->addFlash('error', $error);
        return $this->redirectToRoute(
            'preference_index',
            array()
        );
    }

    /**
     * Envoie un mail aux parents de la famille
     * @param Request $request
     * @param Famille $famille - la famille concernée
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function envoyerMailAction(Request $request, Famille $famille = null)
    {
        $form = $this->createForm(
            MailCovidType::class
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($famille !== null) {
                $tabFamille[] = $famille;
            }
            foreach ($tabFamille as $fam) {
                $tabMails[] = $fam->getParent1()->getEmail();
                if ($fam->getParent2()) {
                    $tabMails[] = $fam->getParent2()->getEmail();
                }
            }
            $this->get('communication')->envoyerMail(
                $tabMails,
                $form->getData()['sujet'],
                $form->getData()['message'],
                null,
                $this->getUser()->getEmail()
            );

            if ($famille !== null) {
                $succes = 'Votre email a bien été envoyé à la famille';
                $this->addFlash('success', $succes);
                return $this->redirectToRoute(
                    'famille_liste',
                    array()
                );
            }
        }
        if ($famille !== null) {
            $titre = 'Envoyer un email à la famille ';
            $titre .= $famille->getNom();
        }

        return $this->render(
            '@Balancelle/Communication/email_covid.html.twig',
            array(
                'form' => $form->createView(),
                'titre' => $titre
            )
        );
    }
}
