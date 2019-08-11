<?php

namespace BalancelleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use InfluxDB;

class DefaultController extends Controller implements FamilleInterface
{
    public function indexAction()
    {
        $t = microtime(true);
        $client = new InfluxDB\Client('localhost');
        $database = $client->selectDB('bench');
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
        $database->writePoints($points);

        return $this->render('@Balancelle/Default/index.html.twig', array(
            'famille' => $this->get('session')->get('famille')
        ));
    }

}
