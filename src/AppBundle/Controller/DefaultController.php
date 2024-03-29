<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Evenements;
use AppBundle\Entity\Revuepresse;

class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        $repository = $this->getDoctrine()->getRepository(Evenements::class);
        $evenements = $repository->findAllActiveEvenement();
        return $this->render(
            '@App/Default/index.html.twig',
            array('evenements' => $evenements)
        );
    }
    
    public function revuePresseAction()
    {
        $repository = $this->getDoctrine()->getRepository(Revuepresse::class);
        $revuepresses = $repository->findAllActiveRevuepresse();
        return $this->render(
            '@App/Default/revuepresse.html.twig',
            array('revuepresses' => $revuepresses)
        );
    }

    public function inscriptionAction()
    {
        return $this->render('@App/Default/inscription.html.twig',array());
    }
}
