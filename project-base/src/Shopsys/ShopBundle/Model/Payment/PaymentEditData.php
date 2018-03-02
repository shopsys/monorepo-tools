<?php

namespace Shopsys\FrameworkBundle\Model\Payment;

class PaymentEditData
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Payment\PaymentData
     */
    public $paymentData;

    /**
     * @var string[]
     */
    public $pricesByCurrencyId;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Payment\PaymentData $paymentData
     * @param array $pricesByCurrencyId
     */
    public function __construct(PaymentData $paymentData = null, array $pricesByCurrencyId = [])
    {
        if ($paymentData !== null) {
            $this->paymentData = $paymentData;
        } else {
            $this->paymentData = new PaymentData();
        }
        $this->pricesByCurrencyId = $pricesByCurrencyId;
    }
}
