<?php

namespace Shopsys\FrameworkBundle\Form\Admin\Order;

use Shopsys\FrameworkBundle\Model\Order\Item\OrderItemData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class OrderItemFormType extends AbstractType
{
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
                'currency' => false,
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter unit price with VAT']),
                ],
                'error_bubbling' => true,
            ])
            ->add('vatPercent', MoneyType::class, [
                'currency' => false,
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
        ]);
    }
}
