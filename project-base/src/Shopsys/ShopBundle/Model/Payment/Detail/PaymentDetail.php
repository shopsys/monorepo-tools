<?php

namespace Shopsys\FrameworkBundle\Model\Payment\Detail;

use Shopsys\FrameworkBundle\Model\Payment\Payment;

class PaymentDetail
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Payment\Payment
     */
    private $payment;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Price[]
     */
    private $basePricesByCurrencyId;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Payment\Payment $payment
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Price[] $basePricesByCurrencyId
     */
    public function __construct(Payment $payment, array $basePricesByCurrencyId)
    {
        $this->payment = $payment;
        $this->basePricesByCurrencyId = $basePricesByCurrencyId;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Payment\Payment
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Price[]
     */
    public function getBasePricesByCurrencyId()
    {
        return $this->basePricesByCurrencyId;
    }
}
