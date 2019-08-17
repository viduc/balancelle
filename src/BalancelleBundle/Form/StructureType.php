<?php

namespace BalancelleBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use BalancelleBundle\Entity\Structure;

class StructureType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('nomCourt')
            ->add('commentaire')
            ->add('active')
            ->add('heureDebutPermanenceMatin')
            ->add('heureFinPermanenceMatin')
            ->add('heureDebutPermanenceAM')
            ->add('heureFinPermanenceAM');
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'validation_groups' => array('Enregistrement'),
            'data_class' => Structure::class,
            'cascade_validation' => true,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'balancellebundle_structure';
    }

}
