<?php

namespace Shopsys\FrameworkBundle\Form\Admin\Payment;

use Shopsys\FrameworkBundle\Form\GroupType;
use Shopsys\FrameworkBundle\Form\PriceTableType;
use Shopsys\FrameworkBundle\Model\Payment\Payment;
use Shopsys\FrameworkBundle\Model\Payment\PaymentData;
use Shopsys\FrameworkBundle\Model\Payment\PaymentFacade;
use Shopsys\FrameworkBundle\Model\Pricing\Currency\CurrencyFacade;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaymentEditFormType extends AbstractType
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Currency\CurrencyFacade
     */
    private $currencyFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Payment\PaymentFacade
     */
    private $paymentFacade;

    public function __construct(CurrencyFacade $currencyFacade, PaymentFacade $paymentFacade)
    {
        $this->currencyFacade = $currencyFacade;
        $this->paymentFacade = $paymentFacade;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $payment = $options['payment'];
        /* @var $payment \Shopsys\FrameworkBundle\Model\Payment\Payment */

        $builderPriceGroup = $builder->create('prices', GroupType::class, [
            'label' => t('Prices'),
            'is_group_container_to_render_as_the_last_one' => true,
        ]);
        $builderPriceGroup
            ->add('pricesByCurrencyId', PriceTableType::class, [
                'currencies' => $this->currencyFacade->getAllIndexedById(),
                'base_prices' => $payment !== null ? $this->paymentFacade->getIndependentBasePricesIndexedByCurrencyId($payment) : [],
            ]);

        $builder
            ->add('paymentData', PaymentFormType::class, [
                'payment' => $payment,
                'render_form_row' => false,
                'inherit_data' => true,
            ])
            ->add($builderPriceGroup)
            ->add('save', SubmitType::class);
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('payment')
            ->setAllowedTypes('payment', [Payment::class, 'null'])
            ->setDefaults([
                'data_class' => PaymentData::class,
                'attr' => ['novalidate' => 'novalidate'],

            ]);
    }
}
