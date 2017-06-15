<?php

namespace Shopsys\ShopBundle\Model\Payment\Detail;

use Shopsys\ShopBundle\Model\Payment\Payment;

class PaymentDetail
{
    /**
     * @var \Shopsys\ShopBundle\Model\Payment\Payment
     */
    private $payment;

    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\Price[]
     */
    private $basePricesByCurrencyId;

    /**
     * @param \Shopsys\ShopBundle\Model\Payment\Payment $payment
     * @param \Shopsys\ShopBundle\Model\Pricing\Price[] $basePricesByCurrencyId
     */
    public function __construct(Payment $payment, array $basePricesByCurrencyId)
    {
        $this->payment = $payment;
        $this->basePricesByCurrencyId = $basePricesByCurrencyId;
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Payment\Payment
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Pricing\Price[]
     */
    public function getBasePricesByCurrencyId()
    {
        return $this->basePricesByCurrencyId;
    }
}
