<?php

namespace BalancelleBundle\Controller;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Swift_Message;
use Symfony\Component\Form\Extension\Templating\TemplatingRendererEngine;
use Twig\Environment;

class PermanenceRappelController extends Controller
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    private $twig;

    /**
     * PermanenceRappelController constructor.
     * @param \Swift_Mailer $mailer
     * @param EntityManagerInterface $em
     * @param Environment $twig
     */
    public function __construct(
        \Swift_Mailer $mailer,
        EntityManagerInterface $em,
        Environment $twig
    ) {
        $this->mailer = $mailer;
        $this->em = $em;
        $this->twig = $twig;
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
            $message = Swift_Message::newInstance()
                ->setSubject($sujet)
                ->setFrom('comptes@labalancelle.yo.fr')
                ->setTo($to)
                ->setContentType('text/html')
                ->setBody(
                    $this->twig->render(
                    '@Balancelle/Permanence/rappel_email.html.twig',
                    array('permanence' => $permanence))
                );
            $this->mailer->send($message);
        }

        return true;
    }
}
