<?php

namespace Shopsys\FrameworkBundle\Model\Payment;

use Shopsys\FrameworkBundle\Model\Pricing\Vat\VatFacade;

class PaymentEditDataFactory
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Payment\PaymentFacade
     */
    private $paymentFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Vat\VatFacade
     */
    private $vatFacade;

    public function __construct(
        PaymentFacade $paymentFacade,
        VatFacade $vatFacade
    ) {
        $this->paymentFacade = $paymentFacade;
        $this->vatFacade = $vatFacade;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Payment\PaymentEditData
     */
    public function createDefault()
    {
        $paymentEditData = new PaymentEditData();
        $paymentEditData->paymentData->vat = $this->vatFacade->getDefaultVat();

        return $paymentEditData;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Payment\Payment $payment
     * @return \Shopsys\FrameworkBundle\Model\Payment\PaymentEditData
     */
    public function createFromPayment(Payment $payment)
    {
        $paymentEditData = new PaymentEditData();
        $paymentData = new PaymentData();
        $paymentData->setFromEntity($payment, $this->paymentFacade->getPaymentDomainsByPayment($payment));
        $paymentEditData->paymentData = $paymentData;

        foreach ($payment->getPrices() as $paymentPrice) {
            $paymentEditData->pricesByCurrencyId[$paymentPrice->getCurrency()->getId()] = $paymentPrice->getPrice();
        }

        return $paymentEditData;
    }
}
