<?php

namespace Shopsys\ShopBundle\Form\Admin\Pricing\Group;

use Shopsys\ShopBundle\Model\Pricing\Group\PricingGroupData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class PricingGroupFormType extends AbstractType
{
    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter pricing group name']),
                ],
            ])
            ->add('coefficient', NumberType::class, [
                'required' => true,
                'scale' => 4,
                'invalid_message' => 'Please enter ratio in correct format',
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter pricing group coefficient']),
                    new Constraints\GreaterThan(['value' => 0, 'message' => 'Coefficient must be greater than 0']),
                ],
            ]);
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PricingGroupData::class,
            'attr' => ['novalidate' => 'novalidate'],
        ]);
    }
}
