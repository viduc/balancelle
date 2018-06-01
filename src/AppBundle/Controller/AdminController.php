<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\File;
use AppBundle\Entity\Evenements;
use AppBundle\Form\EvenementsType;

class AdminController extends Controller
{
    /**
     * Index de la partie admin
     * @return view
     */
    public function indexAction()
    {
        return $this->render('@App/Admin/index.html.twig', array(
            'test' => 'toto'
        ));
    }
 
    /**
     * Gestion des évènements
     * @return view
     */
    public function evenementsListeAction()
    {
        $repository = $this->getDoctrine()->getRepository(Evenements::class);

        $liste = $repository->findAll();
        return $this->render('@App/Admin/evenements_liste.html.twig',
            array('liste' => $liste));
    }

    /**
     * Enregistre un nouvel évènement ou modifie un évènement existant
     * @param Request $request - null - id de l'évènement - form
     * @return view
     */
    public function evenementsSaveAction( Request $request ) {
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
            if ($evenement->getImage() !== null) {
                $file = $evenement->getImage();
                $fileName = $this->generateUniqueFileName().'.';
                $fileName.= $file->guessExtension();
                // moves the file to the directory where images are stored
                move_uploaded_file(
                    $file, 
                    './evenements/'.$fileName
                ) or die("Unable to rename.");
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
        return $this->render('@App/Admin/evenementsAdd.html.twig', array(
            'form' => $form->createView(), 'evenement' => $evenement
        )); 
    }
    
    /**
     * @return string
     */
    private function generateUniqueFileName()
    {
        // md5() reduces the similarity of the file names generated by
        // uniqid(), which is based on timestamps
        return md5(uniqid());
    }
}