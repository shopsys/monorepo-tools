<?php

namespace Shopsys\FrameworkBundle\Model\Payment;

use Shopsys\FrameworkBundle\Model\Pricing\Vat\VatFacade;

class PaymentDataFactory
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Payment\PaymentFacade
     */
    protected $paymentFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Vat\VatFacade
     */
    protected $vatFacade;

    public function __construct(
        PaymentFacade $paymentFacade,
        VatFacade $vatFacade
    ) {
        $this->paymentFacade = $paymentFacade;
        $this->vatFacade = $vatFacade;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Payment\PaymentData
     */
    public function createDefault()
    {
        $paymentData = new PaymentData();
        $paymentData->vat = $this->vatFacade->getDefaultVat();

        return $paymentData;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Payment\Payment $payment
     * @return \Shopsys\FrameworkBundle\Model\Payment\PaymentData
     */
    public function createFromPayment(Payment $payment)
    {
        $paymentData = new PaymentData();
        $paymentData->setFromEntity($payment, $this->paymentFacade->getPaymentDomainsByPayment($payment));

        foreach ($payment->getPrices() as $paymentPrice) {
            $paymentData->pricesByCurrencyId[$paymentPrice->getCurrency()->getId()] = $paymentPrice->getPrice();
        }

        return $paymentData;
    }
}
