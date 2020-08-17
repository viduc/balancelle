<?php

namespace BalancelleBundle\Controller;

use BalancelleBundle\Entity\Annee;
use BalancelleBundle\Entity\Famille;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class NouvelleAnneeController extends AdminController
{
    /**
     * @param null $etape
     * @return RedirectResponse|Response|null
     */
    public function initialiserNouvelleAnneeAction($etape = null)
    {
        $this->session->set('etape', $this->gestionEtapeNouvelleAnnee($etape));
        switch ($this->session->get('etape')) {
            case 0:
                return $this->render(
                    '@Balancelle/Admin/NouvelleAnnee/etape0.html.twig'
                );
                break;
            case 1:
                return $this->redirectToRoute('admin_etapeChangementAnnee');
                break;
            case 2:
                return $this->redirectToRoute('admin_etapeGestionDesFamilles');
                break;
            case 3:
                return $this->redirectToRoute('admin_etapeFin');
                break;
        }

        return null;
    }

    /**
     * Gère les étapes de l'initialisation d'une nouvelle année via un fichier
     * @param String|null $etape - l'étape à enregistrer dans le fichier. Si null
     * le fichier sera créé si il n'existe pas, l'étape enregistrée dans le fichier
     * sera renvoyée
     * @return string - l'étape enregistrée dans le fichier
     */
    public function gestionEtapeNouvelleAnnee($etape = null)
    {
        if (file_exists('nouvelleAnnee')) {
            if ($etape !== null) {
                file_put_contents('nouvelleAnnee', $etape);
            }
        } else {
            file_put_contents('nouvelleAnnee', '0');
        }

        return file_get_contents('nouvelleAnnee', true);
    }

    /**
     * Etape de changement d'année
     * @param Request $request
     * @return RedirectResponse|Response|null
     */
    public function etapeChangementAnneeAction(Request $request)
    {
        $annee = date('Y');
        $annees[$annee-1] = $annee-1;
        $annees[$annee] = $annee;
        $annees[$annee+1] = $annee+1;
        $form = $this->createFormBuilder()
            ->add('annees', ChoiceType::class, ['choices' => $annees])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $this->changerAnnee($data['annees']);
            $this->gestionEtapeNouvelleAnnee(2);
            return $this->redirectToRoute('admin_initialisernouvelleannee');
        }

        return $this->render(
            '@Balancelle/Admin/NouvelleAnnee/etape1.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * Modifie l'année active dans la base
     * Créée l'année si elle n'existe pas
     * Désactive toutes les autres années
     * @param $annee
     */
    public function changerAnnee($annee)
    {
        $anneeEnBase = $this
            ->entityManager
            ->getRepository('BalancelleBundle:Annee')
            ->findBy(['annee' => $annee]);
        if (!$anneeEnBase) {
            $anneeEnBase = new Annee();
            $anneeEnBase->setAnnee($annee);
        } else {
            $anneeEnBase = $anneeEnBase[0];
        }
        $anneeEnBase->setActive(true);
        $this->entityManager->persist($anneeEnBase);
        $this->entityManager->flush();
        $this
            ->entityManager
            ->getRepository('BalancelleBundle:Annee')
            ->desactiverLesAnnesNonValide($annee);
    }


    /**
     * Etape qui gère les familles: renouvellement, permanences
     * @return Response|null
     */
    public function etapeGestionDesFamillesAction()
    {
        $familles = $this
            ->entityManager
            ->getRepository('BalancelleBundle:Famille')
            ->findBy(['active' => 1]);
        $familles = $this->calculerLesPermanencesPourLesFamilles($familles);
        $familles = $this->recupererLesStructuresDesFamilles($familles);
        return $this->render(
            '@Balancelle/Admin/NouvelleAnnee/etape2.html.twig',
            array('familles' => $familles)
        );
    }

    /**
     * Purge les familles - ajax
     * @param Request $request
     * @return JsonResponse
     */
    public function purgerAnneeAnterieureAction(Request $request)
    {
        $purge = $request->get('purge');
        $anne = $this->entityManager->getRepository(
            Annee::class
        )->getAnneeCourante();
        if ($purge !== null) {
            foreach ($purge as $id => $value) {
                if ($id !== 0 && $value !== '') {
                    $famille = $this->entityManager->getRepository(
                        Famille::class
                    )->find($id);
                    if ($value === 'delete') {
                        $famille->setActive(false);
                        $this->entityManager->persist($famille);
                        $this->desactiverLesParentsDuneFamille($famille);
                        $this->desactiverLesEnfantsDuneFamille($famille);
                    } else {
                        $tabFamille = $this->calculerLesPermanencesPourLesFamilles(
                            [$famille]
                        );
                        $famille->setSoldePermanence(
                            $tabFamille[0]->permanenceRestantAfaire
                        );
                        $famille->setNombrePermanence(0);
                        $famille->setAnnee($anne);
                        $this->entityManager->persist($famille);
                    }
                }
            }
            $this->entityManager->flush();
        }

        $this->gestionEtapeNouvelleAnnee(3);
        return new JsonResponse(
            $this->generateUrl('admin_initialisernouvelleannee')
        );
    }

    /**
     * Désactive les parents d'une famille
     * @param Famille $famille
     */
    public function desactiverLesParentsDuneFamille(Famille $famille)
    {
        $this->entityManager->persist($famille->getParent1()->setActive(false));
        if ($famille->getParent2()) {
            $this->entityManager->persist($famille->getParent2()->setActive(false));
        }
        $this->entityManager->flush();
    }

    public function desactiverLesEnfantsDuneFamille(Famille $famille)
    {
        foreach ($famille->getEnfants() as $enfant) {
            $this->entityManager->persist($enfant->setActive(false));
        }
        $this->entityManager->flush();
    }

    /**
     * Etape de fin
     * @return Response|null
     */
    public function etapeFinAction()
    {
        $this->gestionEtapeNouvelleAnnee(0);
        $annee = $this->entityManager->getRepository(
            Annee::class
        )->getAnneeCourante();
        return $this->render(
            '@Balancelle/Admin/NouvelleAnnee/etape3.html.twig',
            array('annee' => $annee)
        );
    }
}
