<?php

namespace BalancelleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $utilisateur = $this->getUser()->getPrenom() . " ";
        $utilisateur .= $this->getUser()->getNom();
        return $this->render('@Balancelle/Default/index.html.twig', array(
            'utilisateur' => $utilisateur
        ));
    }
}
