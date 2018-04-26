<?php

namespace Shopsys\FrameworkBundle\Form\Admin\Payment;

use Shopsys\FrameworkBundle\Form\PriceTableType;
use Shopsys\FrameworkBundle\Model\Payment\Detail\PaymentDetail;
use Shopsys\FrameworkBundle\Model\Payment\PaymentEditData;
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

    public function __construct(CurrencyFacade $currencyFacade)
    {
        $this->currencyFacade = $currencyFacade;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $paymentDetail = $options['payment_detail'];
        /* @var $paymentDetail \Shopsys\FrameworkBundle\Model\Payment\Detail\PaymentDetail */

        $builder
            ->add('paymentData', PaymentFormType::class, [
                'payment' => $paymentDetail !== null ? $paymentDetail->getPayment() : null,
            ])
            ->add('pricesByCurrencyId', PriceTableType::class, [
                'currencies' => $this->currencyFacade->getAllIndexedById(),
                'base_prices' => $paymentDetail !== null ? $paymentDetail->getBasePricesByCurrencyId() : [],
            ])
            ->add('save', SubmitType::class);
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('payment_detail')
            ->setAllowedTypes('payment_detail', [PaymentDetail::class, 'null'])
            ->setDefaults([
                'data_class' => PaymentEditData::class,
                'attr' => ['novalidate' => 'novalidate'],
            ]);
    }
}
