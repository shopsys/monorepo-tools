<?php

namespace Shopsys\ShopBundle\Model\Payment;

use Doctrine\ORM\Mapping as ORM;
use Shopsys\FrameworkBundle\Model\Payment\Payment as BasePayment;
use Shopsys\FrameworkBundle\Model\Payment\PaymentData as BasePaymentData;

/**
 * @ORM\Table(name="payments")
 * @ORM\Entity
 */
class Payment extends BasePayment
{
    /**
     * @param \Shopsys\ShopBundle\Model\Payment\PaymentData $paymentData
     */
    public function __construct(BasePaymentData $paymentData)
    {
        parent::__construct($paymentData);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Payment\PaymentData $paymentData
     */
    public function edit(BasePaymentData $paymentData)
    {
        parent::edit($paymentData);
    }
}
