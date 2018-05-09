<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Evenements;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $repository = $this->getDoctrine()->getRepository(Evenements::class);
        $evenements = $repository->findAllActiveEvenement();
        var_dump($evenements);
        return $this->render(
            '@App/Default/index.html.twig',
            array('evenements' => $evenements)
        );
    }
    
    public function trenteAnsAction()
    {
        return $this->render('@App/Default/30ans.html.twig');
    }
}
