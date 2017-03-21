<?php

namespace Shopsys\ShopBundle\Form\Admin\Order;

use Shopsys\ShopBundle\Model\Order\Item\OrderPaymentData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class OrderPaymentFormType extends AbstractType
{
    /**
     * @var \Shopsys\ShopBundle\Model\Payment\Payment[]
     */
    private $payments;

    /**
     * @param \Shopsys\ShopBundle\Model\Transport\Transport[] $payments
     */
    public function __construct(array $payments)
    {
        $this->payments = $payments;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('payment', ChoiceType::class, [
                'required' => true,
                'choices' => $this->payments,
                'choice_label' => 'name',
                'choice_value' => 'id',
                'choices_as_values' => true, // Switches to Symfony 3 choice mode, remove after upgrade from 2.8
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
            ]);
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => OrderPaymentData::class,
            'attr' => ['novalidate' => 'novalidate'],
        ]);
    }
}
