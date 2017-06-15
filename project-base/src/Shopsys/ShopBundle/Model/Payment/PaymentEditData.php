<?php

namespace Shopsys\ShopBundle\Model\Payment;

use Shopsys\ShopBundle\Model\Payment\PaymentData;

class PaymentEditData
{
    /**
     * @var \Shopsys\ShopBundle\Model\Payment\PaymentData
     */
    public $paymentData;

    /**
     * @var string[]
     */
    public $pricesByCurrencyId;

    /**
     * @param \Shopsys\ShopBundle\Model\Payment\PaymentData $paymentData
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
