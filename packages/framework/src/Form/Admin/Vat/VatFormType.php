<?php

namespace Shopsys\FrameworkBundle\Form\Admin\Vat;

use Shopsys\FrameworkBundle\Model\Pricing\Vat\VatData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class VatFormType extends AbstractType
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_EDIT = 'edit';

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
                    new Constraints\NotBlank(['message' => 'Please enter VAT name']),
                    new Constraints\Length(['max' => 50, 'maxMessage' => 'VAT name cannot be longer than {{ limit }} characters']),
                ],
            ])
            ->add('percent', NumberType::class, [
                'required' => false,
                'scale' => 4,
                'disabled' => $options['scenario'] === self::SCENARIO_EDIT,
                'attr' => [
                    'readonly' => $options['scenario'] === self::SCENARIO_EDIT,
                ],
                'invalid_message' => 'Please enter VAT in correct format.',
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter VAT rate']),
                ],
            ]);
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired('scenario')
            ->setAllowedValues('scenario', [self::SCENARIO_CREATE, self::SCENARIO_EDIT])
            ->setDefaults([
                'data_class' => VatData::class,
                'attr' => ['novalidate' => 'novalidate'],
            ]);
    }
}
