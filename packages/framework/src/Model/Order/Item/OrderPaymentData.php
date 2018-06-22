<?php

namespace Shopsys\FrameworkBundle\Model\Order\Item;

class OrderPaymentData extends OrderItemData
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Payment\Payment|null
     */
    public $payment;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\OrderItem $orderPayment
     */
    public function setFromEntity(OrderItem $orderPayment)
    {
        if ($orderPayment instanceof OrderPayment) {
            $this->payment = $orderPayment->getPayment();
            parent::setFromEntity($orderPayment);
        } else {
            throw new \Shopsys\FrameworkBundle\Model\Order\Item\Exception\InvalidArgumentException(
                'Instance of ' . OrderPayment::class . ' is required as argument.'
            );
        }
    }
}
