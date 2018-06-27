<?php

namespace Shopsys\FrameworkBundle\Model\Payment;

interface PaymentDataFactoryInterface
{
    /**
     * @return \Shopsys\FrameworkBundle\Model\Payment\PaymentData
     */
    public function create(): PaymentData;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Payment\Payment $payment
     * @return \Shopsys\FrameworkBundle\Model\Payment\PaymentData
     */
    public function createFromPayment(Payment $payment): PaymentData;
}
