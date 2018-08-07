<?php

namespace Shopsys\FrameworkBundle\Model\Payment;

class PaymentFactory implements PaymentFactoryInterface
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Payment\PaymentData $data
     * @return \Shopsys\FrameworkBundle\Model\Payment\Payment
     */
    public function create(PaymentData $data): Payment
    {
        return new Payment($data);
    }
}
