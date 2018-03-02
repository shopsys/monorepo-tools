<?php

namespace Shopsys\FrameworkBundle\Form\Admin\Heureka;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class HeurekaShopCertificationFormType extends AbstractType
{
    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('heurekaApiKey', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Constraints\Length([
                        'min' => 32,
                        'max' => 32,
                        'exactMessage' => 'Heureka API must be {{ limit }} characters',
                    ]),
                ],
            ])
            ->add('heurekaWidgetCode', TextareaType::class, [
                'required' => false,
            ])
            ->add('save', SubmitType::class);
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'attr' => ['novalidate' => 'novalidate'],
        ]);
    }
}
