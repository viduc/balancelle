<?php

namespace BalancelleBundle\Controller;

use BalancelleBundle\Entity\Contact;
use BalancelleBundle\Entity\Structure;
use BalancelleBundle\Entity\User;
use Swift_Message;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use BalancelleBundle\Form\ContactType;
//use InfluxDB;

class DefaultController extends Controller implements FamilleInterface
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $famille = $em
            ->getRepository('BalancelleBundle:Famille')
            ->find(1);

        var_dump($em
                     ->getRepository('BalancelleBundle:Famille')
                     ->getParentsEmail($famille));
    /*    $t = microtime(true);
        $client = new InfluxDB\Client('localhost');
        $database = $client->selectDB('bench');
        try {
            $points = [
                new InfluxDB\Point(
                    'duration',
                    microtime(true) - $t
                ),
                new InfluxDB\Point(
                    'memory',
                    memory_get_usage()
                ),
            ];
        } catch (InfluxDB\Database\Exception $e) {
        }
        try {
            $database->writePoints($points);
        } catch (InfluxDB\Exception $e) {//TODO gérer l'erreur ici
        }
*/
        if ($this->get('session')->get('famille') &&
            $this->get('session')->get('famille') !== null) {
            return $this->redirectToRoute('famille_tableauDeBord');
        }

        return $this->render('@Balancelle/Default/index.html.twig', array(
            'famille' => $this->get('session')->get('famille')
        ));
    }

    public function contactAction(Request $request)
    {
        $contact = new Contact();

        $form = $this->createForm(
            ContactType::class,
            $contact
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sujet = '[' .$form->getData()->getPrenom();
            $sujet .= ' ' .$form->getData()->getNom() . '] ';
            $message = Swift_Message::newInstance()
                ->setSubject($sujet . $form->getData()->getSujet())
                ->setFrom('comptes@labalancelle.yo.fr')
                ->setReplyTo($form->getData()->getEmail())
                ->setTo('help@labalancelle.yo.fr')
                ->setContentType('text/html')
                ->setBody($form->getData()->getMessage());
            $this->get('mailer')->send($message);
            return $this->render(
                '@Balancelle/Default/contact.html.twig',
                array('reponse' => 'ok' )
            );
        }

        return $this->render(
            '@Balancelle/Default/contact.html.twig',
            array('form' => $form->createView() )
        );
    }

}
