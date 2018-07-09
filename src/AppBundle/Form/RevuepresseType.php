<?php

namespace AppBundle\Form;

use AppBundle\Entity\Revuepresse;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class RevuepresseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', null, array(
                'widget' => 'single_text',
                'required' => true))
            ->add('titre', null, array(
                'required' => true))
            ->add(
                'image',
                FileType::class,
                array(
                    'label' => "Photo de l'article",
                    'required' => false
                )
            )
            ->add(
                'scan',
                FileType::class,
                array(
                    'label' => "Scan de l'article",
                    'required' => false
                )
            )
            ->add('url', null, array(
                'required' => false))
            ->add('media', null, array(
                'required' => false))
            ->add('information', TextareaType::class, array(
                'required' => false))
            ->add('auteur', null, array(
                'required' => false))
            ->add('active', null)
            ->add('sauvegarder', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Revuepresse::class,
        ));
    }

}
