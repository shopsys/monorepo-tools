<?php

namespace Shopsys\FrameworkBundle\Model\Order\Item;

class OrderPaymentData extends OrderItemData
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Payment\Payment|null
     */
    public $payment;
}
