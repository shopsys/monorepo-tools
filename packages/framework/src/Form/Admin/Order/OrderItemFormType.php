<?php

namespace Shopsys\FrameworkBundle\Form\Admin\Order;

use Shopsys\FrameworkBundle\Form\Transformers\InverseTransformer;
use Shopsys\FrameworkBundle\Form\ValidationGroup;
use Shopsys\FrameworkBundle\Model\Order\Item\OrderItemData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class OrderItemFormType extends AbstractType
{
    public const VALIDATION_GROUP_NOT_USING_PRICE_CALCULATION = 'notUsingPriceCalculation';

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter name']),
                ],
                'error_bubbling' => true,
            ])
            ->add('catnum', TextType::class, [
                'constraints' => [
                    new Constraints\Length(['max' => '255']),
                ],
                'error_bubbling' => true,
            ])
            ->add('priceWithVat', MoneyType::class, [
                'scale' => 6,
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter unit price with VAT']),
                ],
                'error_bubbling' => true,
            ])
            ->add('priceWithoutVat', MoneyType::class, [
                'scale' => 6,
                'constraints' => [
                    new Constraints\NotBlank([
                        'message' => 'Please enter unit price without VAT',
                        'groups' => [self::VALIDATION_GROUP_NOT_USING_PRICE_CALCULATION],
                    ]),
                ],
                'error_bubbling' => true,
            ])
            ->add('totalPriceWithVat', MoneyType::class, [
                'scale' => 6,
                'constraints' => [
                    new Constraints\NotBlank([
                        'message' => 'Please enter total price with VAT',
                        'groups' => [self::VALIDATION_GROUP_NOT_USING_PRICE_CALCULATION],
                    ]),
                ],
                'error_bubbling' => true,
            ])
            ->add('totalPriceWithoutVat', MoneyType::class, [
                'scale' => 6,
                'constraints' => [
                    new Constraints\NotBlank([
                        'message' => 'Please enter total price without VAT',
                        'groups' => [self::VALIDATION_GROUP_NOT_USING_PRICE_CALCULATION],
                    ]),
                ],
                'error_bubbling' => true,
            ])
            ->add(
                $builder->create('setPricesManually', CheckboxType::class, [
                    'property_path' => 'usePriceCalculation',
                ])->addModelTransformer(new InverseTransformer())
            )
            ->add('vatPercent', NumberType::class, [
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter VAT rate']),
                ],
                'error_bubbling' => true,
            ])
            ->add('quantity', IntegerType::class, [
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter quantity']),
                    new Constraints\GreaterThan(['value' => 0, 'message' => 'Quantity must be greater than {{ compared_value }}']),
                ],
                'error_bubbling' => true,
            ])
            ->add('unitName', TextType::class, [
                'constraints' => [
                    new Constraints\Length(['max' => 10]),
                ],
                'error_bubbling' => true,
            ]);
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => OrderItemData::class,
            'attr' => ['novalidate' => 'novalidate'],
            'validation_groups' => function (FormInterface $form) {
                return self::resolveValidationGroups($form);
            },
        ]);
    }

    /**
     * @param \Symfony\Component\Form\FormInterface $form
     * @return string[]
     */
    public static function resolveValidationGroups(FormInterface $form): array
    {
        $validationGroups = [ValidationGroup::VALIDATION_GROUP_DEFAULT];

        /** @var \Shopsys\FrameworkBundle\Model\Order\Item\OrderItemData $orderItemData */
        $orderItemData = $form->getData();

        if (!$orderItemData->usePriceCalculation) {
            $validationGroups[] = self::VALIDATION_GROUP_NOT_USING_PRICE_CALCULATION;
        }

        return $validationGroups;
    }
}
