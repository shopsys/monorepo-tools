<?php

namespace Shopsys\ShopBundle\Form\Admin\Order;

use Shopsys\ShopBundle\Form\FormType;
use Shopsys\ShopBundle\Model\Order\Item\OrderItemData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class OrderItemFormType extends AbstractType
{

    /**
     * @return string
     */
    public function getName() {
        return 'order_item_form';
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('name', FormType::TEXT, [
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter name']),
                ],
                'error_bubbling' => true,
            ])
            ->add('catnum', FormType::TEXT, [
                'constraints' => [
                    new Constraints\Length(['max' => '255']),
                ],
                'error_bubbling' => true,
            ])
            ->add('priceWithVat', FormType::MONEY, [
                'currency' => false,
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter unit price with VAT']),
                ],
                'error_bubbling' => true,
            ])
            ->add('vatPercent', FormType::MONEY, [
                'currency' => false,
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter VAT rate']),
                ],
                'error_bubbling' => true,
            ])
            ->add('quantity', FormType::INTEGER, [
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter quantity']),
                    new Constraints\GreaterThan(['value' => 0, 'message' => 'Quantity must be greater than {{ compared_value }}']),
                ],
                'error_bubbling' => true,
            ])
            ->add('unitName', FormType::TEXT, [
                'constraints' => [
                    new Constraints\Length(['max' => 10]),
                ],
                'error_bubbling' => true,
            ]);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults([
            'data_class' => OrderItemData::class,
            'attr' => ['novalidate' => 'novalidate'],
        ]);
    }

}
