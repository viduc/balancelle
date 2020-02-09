<?php

namespace BalancelleBundle\Controller;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Swift_Message;
use Symfony\Component\Form\Extension\Templating\TemplatingRendererEngine;
use Twig\Environment;
use BalancelleBundle\Controller\CommunicationController;

class PermanenceRappelController extends AppController
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var
     */
    private $com;

    /**
     * PermanenceRappelController constructor.
     * @param \Swift_Mailer $mailer
     * @param EntityManagerInterface $em
     * @param Environment $twig
     */
    public function __construct(
        \Swift_Mailer $mailer,
        EntityManagerInterface $em,
        Environment $twig,
        CommunicationController $com
    ) {
        $this->mailer = $mailer;
        $this->em = $em;
        $this->twig = $twig;
        $this->com = $com;
    }

    /**
     * Envoie un rappel aux familles qui ont une permanence à réaliser dans $nbrJours
     * @param Int $nbrJours
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function rappel($nbrJours)
    {
        $dedut = new DateTime();
        $dedut->modify('+' . $nbrJours . ' days');
        $fin = new DateTime();
        $nbrJours++;
        $fin->modify('+' . $nbrJours . ' days');
        $permanences = $this->em->getRepository('BalancelleBundle:Permanence')
            ->recupererToutesLesPermanencesPourRappel($dedut, $fin);
        $sujet = 'La Balancelle - votre permanence du ' . $dedut->format('d-m-Y');
        foreach ($permanences as $permanence) {
            $to[] = $permanence->getFamille()->getParent1()->getEmail();
            if ($permanence->getFamille()->getParent2() !== null) {
                $to[] = $permanence->getFamille()->getParent2()->getEmail();
            }
            $this->com->envoyerMail(
                $to,
                $sujet,
                null,
                null,
                $permanence
                    ->getSemaine()
                    ->getCalendrier()
                    ->getStructure()
                    ->getEmail(),
                $this->twig->render(
                    '@Balancelle/Communication/rappel_email.html.twig',
                    array('permanence' => $permanence))
            );
            $to = null;
        }

        return true;
    }
}
