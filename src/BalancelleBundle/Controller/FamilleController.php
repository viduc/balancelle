<?php

namespace BalancelleBundle\Controller;

use BalancelleBundle\Entity\Enfant;
use BalancelleBundle\Entity\Famille;
use BalancelleBundle\Entity\Permanence;
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
        $repositoryPermanence = $this->em->getRepository(
            'BalancelleBundle:Permanence'
        );
        foreach ($familles as $famille) {
            $tableauFamilles[$id]['famille'] = $famille;
            $tableauFamilles[$id]['permanences'] =
                $repositoryPermanence->getInformationPermanenceFamille($famille);
            $tableauFamilles[$id]['listePermanence'] =
                $repositoryPermanence->formaterListePermanence(
                    $tableauFamilles[$id]['permanences']
                );
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

}
