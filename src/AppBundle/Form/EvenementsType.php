<?php

namespace AppBundle\Form;

use AppBundle\Entity\Evenements;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class EvenementsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', null, array(
                'widget' => 'single_text',
                'required' => true))
            ->add('heure', null, array(
                'widget' => 'single_text',
                'required' => false))
            ->add('titre', null, array(
                'required' => true))
            ->add(
                'image',
                FileType::class,
                array(
                    'label' => "Photo de l'évènement",
                    'required' => false
                )
            )
            ->add('lieu', null, array(
                'required' => false))
            ->add('information', TextareaType::class, array(
                'required' => false))
            ->add('active', null)
            ->add('sauvegarder', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Evenements::class,
        ));
    }

}
