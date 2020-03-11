<?php

namespace BalancelleBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class PermanenceRappelCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $description = 'Envoie un rappel aux familles qui ont une permanence à';
        $description .= ' réaliser dans x jours (valeur passée en paramètre). ';
        $this
            ->setName('balancelle:permanence_rappel_command')
            ->setDescription($description)
            /*->addArgument(
                'nbrJour',
                InputArgument::REQUIRED,
                'Relancer les familles qui ont une permanence dans combien de jours?'
            )*/
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $controller = $this->getContainer()->get(
            'BalancelleBundle\Controller\PermanenceRappelController'
        );
        try {
            $output->writeln($controller->rappel(1));//$input->getArgument('nbrJour')));
        } catch (LoaderError $e) {
        } catch (RuntimeError $e) {
        } catch (SyntaxError $e) {
        }
    }
}
