<?php

namespace Shopsys\FrameworkBundle\Model\Order\Item;

interface OrderPaymentDataFactoryInterface
{
    /**
     * @return \Shopsys\FrameworkBundle\Model\Order\Item\OrderPaymentData
     */
    public function create(): OrderPaymentData;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\OrderPayment $orderPayment
     * @return \Shopsys\FrameworkBundle\Model\Order\Item\OrderPaymentData
     */
    public function createFromOrderPayment(OrderPayment $orderPayment): OrderPaymentData;
}
