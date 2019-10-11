<?php

namespace BalancelleBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use BalancelleBundle\Entity\User;

class UserType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'balancellebundle_user';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('civilite', ChoiceType::class, [
                'choices'  => [
                    'Mr' => 'Mr',
                    'Mme' => 'Mme'
                ],
            ])
            ->add('prenom')
            ->add('nom')
            ->add(
                'birthday',
                DateType::class,
                [
                    'widget' => 'single_text',
                    'attr' => ['class' => 'js-datepicker', 'type' => 'date'],
                    'html5' => false,
                    'format' => 'dd/MM/yyyy'
                ]
            )
            ->add('email')
            ->add('enabled', CheckboxType::class, ['required' => false])
            ->add('roleAdmin', CheckboxType::class, ['required' => false])
            ->add('rolePro', CheckboxType::class, ['required' => false])
        ;
    }
}
