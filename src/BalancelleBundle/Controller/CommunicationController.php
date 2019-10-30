<?php

namespace BalancelleBundle\Controller;

use Swift_Message;
use BalancelleBundle\Entity\Structure;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use BalancelleBundle\Entity\User;
use Twig\Environment;

class CommunicationController extends Controller
{
    protected $em;
    protected $mailer;
    protected $twig;

    public function __construct(
        EntityManagerInterface $em,
        \Swift_Mailer $mailer,
        Environment $twig
    ) {
        $this->em = $em;
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function envoyerMailStructure(Structure $structure, $sujet, $message)
    {
        $listeUser = $this->em->getRepository(
            User::class
        )->recupererLesUtilisateursViaStructure($structure);
        $listeMail = [];
        foreach ($listeUser as $user) {
            $listeMail[] = $user->getEmail();
        }
        $this->envoyerMail($listeMail, $sujet, $message);
        return $listeUser;
    }

    public function envoyerMail(array $listeMail, string $sujet, string $message)
    {
        $mail = Swift_Message::newInstance()
            ->setSubject('[La Balancelle] - ' . $sujet)
            ->setFrom('comptes@labalancelle.yo.fr')
            ->setTo($listeMail)
            ->setContentType('text/html')
            //->attach(\Swift_Attachment::fromPath($pdf, 'application/pdf'))
            ->setBody(
                $this->twig->render(
                    '@Balancelle/Default/email.html.twig',
                    array('message' => $message, 'sujet' => $sujet)
                )
            );
        $this->mailer->send($mail);
    }
}
