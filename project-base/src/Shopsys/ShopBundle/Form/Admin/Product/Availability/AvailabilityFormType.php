<?php

namespace Shopsys\ShopBundle\Form\Admin\Product\Availability;

use Shopsys\ShopBundle\Form\Locale\LocalizedType;
use Shopsys\ShopBundle\Model\Product\Availability\AvailabilityData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class AvailabilityFormType extends AbstractType
{
    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', LocalizedType::class, [
                'required' => true,
                'options' => [
                    'constraints' => [
                        new Constraints\NotBlank(['message' => 'Please enter availability name in all languages']),
                        new Constraints\Length(['max' => 100, 'maxMessage' => 'Availability name cannot be longer than {{ limit }} characters']),
                    ],
                ],
            ])
            ->add('dispatchTime', NumberType::class, [
                'scale' => 0,
                'required' => false,
                'invalid_message' => 'Number of days to expedite must be whole number.',
                'constraints' => [
                    new Constraints\GreaterThanOrEqual([
                        'value' => 0, 'message' => 'Number of days to despatch must be greater or equal to {{ compared_value }}',
                    ]),
                ],
            ]);
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AvailabilityData::class,
            'attr' => ['novalidate' => 'novalidate'],
        ]);
    }
}
