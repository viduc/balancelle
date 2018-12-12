<?php

namespace BalancelleBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
class UserType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'BalancelleBundle\Entity\User'
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
            ->add('prenom')
            ->add('nom')
            ->add(
                'birthday',
                DateType::class,
                [
                    'widget' => 'single_text',
                    'attr' => ['class' => 'js-datepicker'],
                    'html5' => false,
                    'format' => 'dd/MM/yyyy'
                ])
            ->add('email')
            ->add('enabled', CheckboxType::Class,['required' => false])
            ->add('roleAdmin', CheckboxType::Class,['required' => false])
            ->add('rolePro', CheckboxType::Class,['required' => false])
            ->add('roleParent', CheckboxType::Class,['required' => false])
        ;
    }
}
