<?php

namespace BalancelleBundle\Form;

use BalancelleBundle\Entity\Structure;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use BalancelleBundle\Entity\Calendrier;

class CalendrierType extends AbstractType
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //$tabAnnee = array_combine(range(2018,2118), range(2018,2118));
        $builder
            ->add(
                'dateDebut',
                DateType::class,
                [
                    'widget' => 'single_text',
                    'attr' => ['class' => 'js-datepicker', 'type' => 'date'],
                    'html5' => false,
                    'format' => 'dd/MM/yyyy'
                ]
            )
            ->add(
                'dateFin',
                DateType::class,
                [
                    'widget' => 'single_text',
                    'attr' => ['class' => 'js-datepicker', 'type' => 'date'],
                    'html5' => false,
                    'format' => 'dd/MM/yyyy'
                ]
            )
            ->add('commentaire', TextareaType::class, [])
            ->add('active')
            ->add('structure', EntityType::class, [
                'choices' => $this->entityManager->getRepository(
                    Structure::class
                )->getStructureActive(),
                'class' => Structure::class,
                'choice_label' => 'nom',
            ])
            ->add('nbrPermanenceMatin', ChoiceType::class, [
                'choices'  => [
                    '0' => 0,
                    '1' => 1,
                    '2' => 2,
                    '3' => 3,
                ],
            ])
            ->add('nbrPermanenceAM', ChoiceType::class, [
                'choices'  => [
                    '0' => 0,
                    '1' => 1,
                    '2' => 2,
                    '3' => 3,
                ],
            ]);
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Calendrier::class
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'balancellebundle_calendrier';
    }
}
