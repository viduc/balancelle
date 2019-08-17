<?php

namespace BalancelleBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use BalancelleBundle\Entity\Permanence;

class PermanenceType extends AbstractType
{
    protected $desactiver = true;

    /**
     * PermanenceType constructor.
     * @param AuthorizationCheckerInterface $auth
     */
    public function __construct(AuthorizationCheckerInterface $auth)
    {
        if ($auth->isGranted('ROLE_ADMIN')) {
            $this->desactiver = false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre', TextType::class, ['disabled' => $this->desactiver])
            ->add(
                'debut',
                DateType::class,
                [
                    'widget' => 'single_text',
                    'html5' => false,
                    'format' => 'dd/MM/yyyy HH-mm-ss',
                    'disabled' => true
                ]
            )
            ->add(
                'fin',
                DateType::class,
                [
                    'widget' => 'single_text',
                    'html5' => false,
                    'format' => 'dd/MM/yyyy HH-mm-ss',
                    'disabled' => true
                ]
            )
            ->add(
                'commentaire',
                TextType::class,
                ['disabled' => $this->desactiver]
            );
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Permanence::class
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'balancellebundle_permanence';
    }


}
