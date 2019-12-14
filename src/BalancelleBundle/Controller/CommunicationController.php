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

    /**
     * Envoie un mail à tout les parents d'une structure
     * @param $structure - la structure
     * @param String $sujet - le sujet du mail
     * @param String $message - le message au format html
     * @param null|array $fichiers - chemin du fichier pdf (pièce jointe)
     * @return mixed
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function envoyerMailStructure(
        $structure,
        $sujet,
        $message,
        array $fichiers = null)
    {
        $listeUser = $this->em->getRepository(
            User::class
        )->recupererLesUtilisateursViaStructure($structure);
        $listeMail = [];
        foreach ($listeUser as $user) {
            $listeMail[] = $user->getEmail();
        }
        $from = null;
        if ($structure !== null) {
            $from = $structure->getEmail();
        }
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
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
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
                $explode = (explode('/',$file));
                $name = end($explode);
            }
            if (!$fileExist) {
                $tabRetour[$i]['erreur']['erreurFichierPath'] =
                    'Le fichier n\'existe pas ';
                $tabRetour[$i]['valide'] = false;
            }
            else {
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
}
