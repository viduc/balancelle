<?php

namespace BalancelleBundle\Controller;

use BalancelleBundle\Entity\Course;
use Swift_Message;
use BalancelleBundle\Entity\Structure;
use Doctrine\ORM\EntityManagerInterface;
use BalancelleBundle\Entity\User;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class CommunicationController extends AppController
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

    /**
     * Envoie un mail à tout les parents d'une structure
     * @param $structure - la structure
     * @param String $sujet - le sujet du mail
     * @param String $message - le message au format html
     * @param null|array $fichiers - chemin du fichier pdf (pièce jointe)
     * @return mixed
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function envoyerMailStructure(
        $structure,
        $sujet,
        $message,
        array $fichiers = null
    ) {
        $listeUser = $this->em->getRepository(
            User::class
        )->recupererLesUtilisateursViaStructure($structure);
        $listeMail = [];
        foreach ($listeUser as $user) {
            $listeMail[] = $user->getEmail();
        }
        $from = $structure->getEmail();
        $listeMail[] = $structure->getEmail();
        return $this->envoyerMail(
            $listeMail,
            $sujet,
            $message,
            $fichiers,
            $from
        );
    }

    /**
     * Envoie un mail avec le template générique balancelle
     * @param array $listeMail - une liste d'adresse mail
     * @param String $sujet - le sujet du mail
     * @param String $message - le message du mail au format html
     * @param null|array $fichier - chemin du fichier pdf (pièce jointe)
     * @param String|null $from - l'adresse de l'expéditeur
     * @param null $body
     * @return mixed
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function envoyerMail(
        array $listeMail,
        $sujet,
        $message = null,
        array $fichier = null,
        $from = null,
        $body = null
    ) {
        $tabRetour = $this->verifierFichier($fichier);

        if ($from === null) {
            $from = 'comptes@labalancelle.yo.fr';
        }
        $email = Swift_Message::newInstance()
            ->setSubject('[La Balancelle] - ' . $sujet)
            ->setFrom($from)
            //->setTo($mail)
            ->setContentType('text/html');
        if ($body !== null) {
            $email->setBody($body);
        } else {
            $email->setBody(
                $this->twig->render(
                    '@Balancelle/Communication/email.html.twig',
                    array('message' => $message, 'sujet' => $sujet)
                )
            );
        }
        foreach ($tabRetour as $retour) {
            if ($retour['valide']) {
                $email->attach($retour['attachement']);
            }
        }

        foreach ($listeMail as $mail) {
            $email->setTo($mail);
            $this->mailer->send($email);
        }

        return $tabRetour;
    }

    public function envoyerMailEchangePermanence(
        $permanence,
        $action,
        $famille = null,
        $url = null
    ) {
        $structure = $permanence->getSemaine()->getCalendrier()->getStructure();
        $to[] = $structure->getEmail();

        if ($action === "propose") {
            $sujet = 'Permanence proposée à l\'échange';
            $message = 'La famille: ' . $permanence->getFamille()->getNom();
            $message .= ' a proposé sa permanence du ';
            $message .= '<a href="'. $url . '">';
            $message .= $permanence->getDebut()->format('d/m/Y H:i:s');
            $message .= '</a> à l\'échange';
            $this->envoyerMail($to, $sujet, $message);
            $message = 'La famille inscrite à la permanence du: ';
            $message .= $permanence->getDebut()->format('d/m/Y H:i:s');
            $message .= ' souhaite proposer sa place suite à une contrainte ';
            $message .= 'personnelle. </br> Nous cherchons une famille ';
            $message .= 'suceptible de réaliser cette permanence. </br>Vous ';
            $message .= 'pouvez vous inscrire à cette permanence en cliquant ';
            $message .= 'sur ce <a href="'. $url . '">lien</a> </br>';
            $message .= 'Nous vous remercions par avance pour votre contribution';
            $message .= ' au bon fonctionnement de la structure et pour votre';
            $message .= ' aide aportée à cette famille';
            $this->envoyerMailStructure($structure, $sujet, $message);
        } elseif ($action === "accepte") {
            $sujet = 'Echange d\'une permanence';
            $message = 'La permanence du:  <a href="'. $url . '">';
            $message .= $permanence->getDebut()->format('d/m/Y H:i:s');
            $message .= '</a> a été échangé entre la famille ';
            $message .= $permanence->getFamille()->getNom();
            $message .= ' et la famille ';
            $message .= $famille->getNom();
            $this->envoyerMail(
                $to,
                $sujet,
                $message
            );
            /* envoie d'un mail à la famille qui avait proposé l'échange */
            $message = 'La permanence du: ';
            $message .= $permanence->getDebut()->format('d/m/Y H:i:s');
            $message .= ' que vous aviez proposé à l\'échange ';
            $message .= 'vient d\'être acceptée par une autre famille. </br>';
            $message .= 'Vous n\'êtes donc plus inscrit à cette permanence';
            $sujet = 'Votre permanence proposé à l\'échange a été accepté par ';
            $sujet .= 'une autre famille';

            $this->envoyerMail(
                $this->em
                    ->getRepository('BalancelleBundle:Famille')
                    ->getParentsEmail($permanence->getFamille()),
                $sujet,
                $message
            );
        }
    }

    /**
     * vérifie les fichiers joins
     * @param null|array(path) $fichiers - les chemin des fichiers pdf
     * @return mixed
     */
    public function verifierFichier(array $fichiers = null)
    {
        $tabRetour = [];

        if ($fichiers === null || !count($fichiers)) {
            return $tabRetour;
        }

        $lengthFichiers = count($fichiers);
        for ($i=0; $i<$lengthFichiers; $i++) {
            if (isset($fichiers[$i]['file'])) {
                $file = $fichiers[$i]['file'];
                $fileExist = file_exists($file->getPathName());
                $pathName = $file->getPathName();
                $mimeType = $file->getMimeType();
                $name = $file->getClientOriginalName();
            } else {
                $file = $fichiers[$i];
                $fileExist = file_exists($file);
                $pathName = $file;
                $mimeType = mime_content_type($file);
                $explode = (explode('/', $file));
                $name = end($explode);
            }
            if (!$fileExist) {
                $tabRetour[$i]['erreur']['erreurFichierPath'] =
                    'Le fichier n\'existe pas ';
                $tabRetour[$i]['valide'] = false;
            } else {
                $tabRetour[$i]['attachement'] = \Swift_Attachment::fromPath(
                    $pathName,
                    $mimeType
                )->setFilename($name)
                ;
                $tabRetour[$i]['valide'] = true;
            }
        }

        return $tabRetour;
    }

    /**
     * Envoie un email pour les courses aux parents de la famille concernée
     * @param Course $course
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function envoyerMailCourse(Course $course)
    {
        $mails = $this->em
            ->getRepository('BalancelleBundle:Famille')
            ->getParentsEmail(
                $course->getFamille()
            );
        $sujet = 'Vous êtes inscrit aus courses';
        $email = Swift_Message::newInstance()
            ->setSubject('[La Balancelle] - ' . $sujet)
            ->setFrom($course->getStructure()->getEmail())
            ->setContentType('text/html');
        $email->setBody(
            $this->twig->render(
                '@Balancelle/Communication/email_course.html.twig',
                array('course' => $course)
            )
        );
        foreach ($mails as $mail) {
            $email->setTo($mail);
            $this->mailer->send($email);
        }
    }
}
