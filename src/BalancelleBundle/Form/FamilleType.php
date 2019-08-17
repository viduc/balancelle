<?php

namespace BalancelleBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use BalancelleBundle\Entity\Famille;

class FamilleType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('active')
            ->add(
                'parent1')
                 /*EntityType::class,
                    [
                    'class' => User::class,
                    'query_builder' => function(EntityRepository $userRepository)
                        {
                            return $userRepository->findByRoles('ROLE_ADMIN');
                        }
                    ]
            )*/
            ->add('parent2')
            ->add('nombrePermanence', ChoiceType::class, [
                'choices'  => [
                    '0' => 0,
                    '1' => 1,
                    '2' => 2,
                    '3' => 3,
                    '4' => 4,
                    '5' => 5,
                    '6' => 6,
                    '7' => 7,
                    '8' => 8,
                    '9' => 9,
                    '10' => 10,
                    '11' => 11,
                    '12' => 12,
                    '13' => 13,
                    '14' => 14,
                    '15' => 15,
                    '16' => 16,
                    '17' => 17,
                    '18' => 18,
                    '19' => 19,
                    '20' => 20
                ],
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Famille::class
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'balancellebundle_famille';
    }


}
