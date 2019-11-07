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
     * @param Structure $structure - la structure
     * @param String $sujet - le sujet du mail
     * @param String $message - le message au format html
     * @param null|array $fichiers - chemin du fichier pdf (pièce jointe)
     * @return mixed
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function envoyerMailStructure(
        Structure $structure,
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

        return $this->envoyerMail($listeMail, $sujet, $message, $fichiers);
    }

    /**
     * Envoie un mail avec le template générique balancelle
     * @param array $listeMail - une liste d'adresse mail
     * @param String $sujet - le sujet du mail
     * @param String $message - le message du mail au format html
     * @param null|array $fichier - chemin du fichier pdf (pièce jointe)
     * @return mixed
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function envoyerMail(
        array $listeMail,
        $sujet,
        $message,
        array $fichier = null
    ) {
        $tabRetour = $this->verifierFichier($fichier);

        foreach ($listeMail as $mail) {
            $email = Swift_Message::newInstance()
                ->setSubject('[La Balancelle] - ' . $sujet)
                ->setFrom('comptes@labalancelle.yo.fr')
                ->setTo($mail)
                ->setContentType('text/html')
                ->setBody(
                    $this->twig->render(
                        '@Balancelle/Default/email.html.twig',
                        array('message' => $message, 'sujet' => $sujet)
                    )
                );
            foreach ($tabRetour as $retour) {
                if ($retour['valide']) {
                    $email->attach($retour['attachement']);
                }
            }

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
            $file = $fichiers[$i]['file'];
            if (!file_exists($file->getPathName())) {
                $tabRetour[$i]['erreur']['erreurFichierPath'] =
                    'Le fichier n\'existe pas ';
                $tabRetour[$i]['valide'] = false;
            }
            /*elseif (!strpos($fichiers[$i], '.pdf')) {
                $tabRetour[$i]['erreur']['erreurFichierFormat'] =
                    'Le chemin du fichier est non conforme';
                $tabRetour[$i]['valide'] = false;
            }*/
            else {
                $tabRetour[$i]['attachement'] = \Swift_Attachment::fromPath(
                    $file->getPathName(),
                    $file->getMimeType()
                )->setFilename($file->getClientOriginalName());
                $tabRetour[$i]['valide'] = true;
            }
        }

        return $tabRetour;
    }
}
