<?php

namespace BalancelleBundle\Form;

use BalancelleBundle\Entity\Structure;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use BalancelleBundle\Entity\Course;
use BalancelleBundle\Entity\Magasin;
use BalancelleBundle\Entity\Famille;

class CourseType extends AbstractType
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
        $builder
            ->add('magasin', EntityType::class, [
                'choices' => $this->entityManager->getRepository(
                    Magasin::class
                )->getMagasinActive(),
                'class' => Magasin::class,
                'choice_label' => 'nom',
            ])
            ->add('famille', EntityType::class, [
                'choices' => $this->entityManager->getRepository(
                    Famille::class
                )->getFamilleActive(),
                'class' => Famille::class,
                'choice_label' => 'nom',
            ])
            ->add('structure', EntityType::class, [
                'choices' => $this->entityManager->getRepository(
                    Structure::class
                )->getStructureActive(),
                'class' => Structure::class,
                'choice_label' => 'nom',
            ])
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
            ->add('active');
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'validation_groups' => array('Enregistrement'),
            'data_class' => Course::class,
            'cascade_validation' => true,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'balancellebundle_course';
    }

}
