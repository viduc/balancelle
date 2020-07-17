<?php

namespace BalancelleBundle\Controller;

use BalancelleBundle\Entity\Course;
use BalancelleBundle\Entity\Permanence;
use BalancelleBundle\Entity\Structure;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends Controller implements MenuInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function tableauDeBordAction($structureId)
    {
        $em = $this->getDoctrine()->getManager();
        if ($structureId === null || $structureId === 'all') {
            $familles = $em
                ->getRepository('BalancelleBundle:Famille')
                ->findAll();
            $structureSelectionnee = 'toutes';
        } else {
            $familles = $em
                ->getRepository('BalancelleBundle:Famille')
                ->getFamilleDuneStructure($structureId);
            $structureSelectionnee = $structureId;
        }

        $repository = $this->getDoctrine()->getRepository(Permanence::class);
        foreach ($familles as $famille) {
            $famille->permFaite = $repository->recupererLesPermanencesRealisees(
                $famille
            );
            $famille->nbPermanenceAFaire = $famille->getNombrePermanence();
            $famille->permanenceInscrit = $repository->findByFamille($famille);
            $famille->pourcentagePermanenceFaite = 0;
            if ($famille->nbPermanenceAFaire) {
                $famille->pourcentagePermanenceFaite = count(
                    $famille->permFaite
                )*100/$famille->nbPermanenceAFaire;
                $famille->course = $em
                ->getRepository(Course::class)
                ->recupererLesCoursesDuneFamille($famille);
            }
        }
        $structures = $em->getRepository(Structure::class)->findBy(['active' => 1]);
        return $this->render(
            '@Balancelle/Admin/index.html.twig',
            array(
                'familles' => $familles,
                'structures' => $structures,
                'structureSelectionnee' => $structureSelectionnee
            )
        );
    }

    public function initialiserNouvelleAnneeAction()
    {
        $familles = $this
            ->entityManager
            ->getRepository('BalancelleBundle:Famille')
            ->findAll();
        $familles = $this->calculerLesPermanencesPourLesFamilles($familles);
        return $this->render(
            '@Balancelle/Admin/initialiserNouvelleAnnee.html.twig',
            array('familles' => $familles)
        );
    }

    public function purgerAnneeAnterieureAction(Request $request)
    {
        $purge = $request->get('purge');
        foreach ($purge as $id => $value) {
            if ($value !== '' && $value === 'delete') {
                //TODO methode delete famille
            } elseif ($value !== '') {
                //TODO methode sauvegarde famille + perm
            }
        }
        return new JsonResponse(true);
    }

    /**
     * @param array $familles
     * @return array
     */
    public function calculerLesPermanencesPourLesFamilles(array $familles)
    {
        $repository = $this->entityManager->getRepository(Permanence::class);
        foreach ($familles as $famille) {
            $famille->permFaite = $repository->recupererLesPermanencesRealisees(
                $famille
            );
            $famille->nbPermanenceAFaire = $famille->getNombrePermanence();
            $famille->permanenceInscrit = $repository->findByFamille($famille);
            $famille->pourcentagePermanenceFaite = 0;
            $famille->permanenceRestantAfaire = $famille->nbPermanenceAFaire -
                count($famille->permFaite);
            if ($famille->nbPermanenceAFaire) {
                $famille->pourcentagePermanenceFaite = count(
                    $famille->permFaite
                ) * 100 / $famille->nbPermanenceAFaire;
            }
        }

        return $familles;
    }


    //----------------------> parametrage site vitrine <----------------------//
    /**
     * Index de la partie admin
     * @param CommunicationController $communication
     * @return Response
     */
    /*public function indexAction(CommunicationController $communication)
    {
        $structure = $this->getDoctrine()->getRepository(Structure::class)->find(1);
        return $this->render('@Balancelle/Default/index.html.twig', array(
            'test' => 'test'
        ));
    }*/
 
    /**
     * Gestion des évènements
     * @return Response
     */
    /*public function evenementsListeAction()
    {
        $repository = $this->getDoctrine()->getRepository(Evenements::class);

        $liste = $repository->findAll();
        return $this->render(
            '@Balancelle/Admin/evenements_liste.html.twig',
            array('liste' => $liste)
        );
    }

    /**
     * Enregistre un nouvel évènement ou modifie un évènement existant
     * @param Request $request - null - id de l'évènement - form
     * @return Response
     */
    /*public function evenementsSaveAction(Request $request)
    {
        $image = new File('./evenements/noimage.png');
        if (!$request->attributes->get('idevenement')) {
            $evenement = new Evenements();
        } else {
            $repository = $this->getDoctrine()->getRepository(Evenements::class);
            $evenement = $repository->find(
                $request->attributes->get('idevenement')
            );
            //l'image étant enregistrée sous forme de string, il faut la
            //transformer en objet File
            if ($evenement->getImage() !== null) {
                $image = new File($evenement->getImage());
            }
            $evenement->setImage($image);
        }
        $form = $this->createForm(EvenementsType::class, $evenement);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /* si on a une image de tramnsmise on la récupère */
    /*       if ($evenement->getImage() !== null) {
               $file = $evenement->getImage();
               $fileName = $this->generateUniqueFileName().'.';
               $fileName.= $file->guessExtension();
               // moves the file to the directory where images are stored
               move_uploaded_file(
                   $file,
                   './evenements/'.$fileName
               ) or die('Unable to rename.');
               $evenement->setImage(new File('./evenements/'.$fileName));
           } else { //si pas d'image de transmise on enregistre soit la noimage,
               // soit l'image déjà enregistrée en base si présente
               $evenement->setImage($image);
           }
           $entityManager = $this->getDoctrine()->getManager();
           $entityManager->persist($evenement);
           $entityManager->flush();
               $this->addFlash(
                   'success',
                   "L'évènement a été enregistré"
           );
       }
       return $this->render(
           '@Balancelle/Admin/evenementsAdd.html.twig',
           array('form' => $form->createView(), 'evenement' => $evenement)
       );
    }

    /**
    * Gestion de la revue de presse
    * @return Response
    */
    /*public function revuepresseListeAction()
    {
        $repository = $this->getDoctrine()->getRepository(Revuepresse::class);

        $liste = $repository->findAll();
        return $this->render(
            '@Balancelle/Admin/revuepresse_liste.html.twig',
            array('liste' => $liste)
        );
    }

    /**
     * Enregistre une nouvelle revue de presse ou modifie un évènement existant
     * @param Request $request - null - id de la revue de presse - form
     * @return Response
     */
    /*public function revuepresseSaveAction(Request $request)
    {
        $image = new File('./revuepresse/noimage.png');
        $scan = new File('./revuepresse/scan/noscan.pdf');
        if (!$request->attributes->get('idrevuepresse')) {
            $revuepresse = new Revuepresse();
        } else {
            $repository = $this->getDoctrine()->getRepository(Revuepresse::class);
            $revuepresse = $repository->find(
                $request->attributes->get('idrevuepresse')
            );
            //l'image étant enregistrée sous forme de string, il faut la
            //transformer en objet File
            if ($revuepresse->getImage() !== null) {
                $image = new File($revuepresse->getImage());
            }
            $revuepresse->setImage($image);
            if ($revuepresse->getScan() !== null) {
                $scan = new File($revuepresse->getScan());
            }
            $revuepresse->setScan($scan);
        }
        $form = $this->createForm(RevuepresseType::class, $revuepresse);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /* si on a une image de tramnsmise on la récupère */
    /*       if ($revuepresse->getImage() !== null) {
               $file = $revuepresse->getImage();
               $fileName = $this->generateUniqueFileName().'.';
               $fileName.= $file->guessExtension();
               // moves the file to the directory where images are stored
               move_uploaded_file(
                   $file,
                   './revuepresse/'.$fileName
               ) or die('Unable to rename.');
               $revuepresse->setImage(new File('./revuepresse/'.$fileName));
           } else { //si pas d'image de transmise on enregistre soit la noimage,
               // soit l'image déjà enregistrée en base si présente
               $revuepresse->setImage($image);
           }
           /* si on a un scan de tramnsmise on la récupère */
    /*       if ($revuepresse->getScan() !== null) {
               $file = $revuepresse->getScan();
               $fileName = $this->generateUniqueFileName().'.';
               $fileName.= $file->guessExtension();
               // moves the file to the directory where images are stored
               move_uploaded_file(
                   $file,
                   './revuepresse/scan/'.$fileName
               ) or die('Unable to rename.');
               $revuepresse->setScan(new File('./revuepresse/scan/'.$fileName));
           } else { //si pas d'image de transmise on enregistre soit la noimage,
               // soit l'image déjà enregistrée en base si présente
               $revuepresse->setScan($scan);
           }
           $entityManager = $this->getDoctrine()->getManager();
           $entityManager->persist($revuepresse);
           $entityManager->flush();
               $this->addFlash(
                   'success',
                   'La revue de presse a été enregistré'
           );
       }
       return $this->render(
           '@Balancelle/Admin/revuepresseAdd.html.twig',
           array('form' => $form->createView(), 'revuepresse' => $revuepresse)
       );
    }

    /**
    * @return string
    */
    private function generateUniqueFileName()
    {
        // md5() reduces the similarity of the file names generated by
        // uniqid(), which is based on timestamps
        return md5(uniqid('', true));
    }
}
