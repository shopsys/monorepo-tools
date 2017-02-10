<?php

namespace Shopsys\ShopBundle\Model\Order\Item;

use Shopsys\ShopBundle\Model\Order\Item\OrderItem;
use Shopsys\ShopBundle\Model\Order\Item\OrderItemData;
use Shopsys\ShopBundle\Model\Order\Item\OrderPayment;

class OrderPaymentData extends OrderItemData
{

    /**
     * @var \Shopsys\ShopBundle\Model\Payment\Payment
     */
    public $payment;

    /**
     * @param \Shopsys\ShopBundle\Model\Order\Item\OrderItem $orderPayment
     */
    public function setFromEntity(OrderItem $orderPayment) {
        if ($orderPayment instanceof OrderPayment) {
            $this->payment = $orderPayment->getPayment();
            parent::setFromEntity($orderPayment);
        } else {
            throw new \Shopsys\ShopBundle\Model\Order\Item\Exception\InvalidArgumentException(
                'Instance of ' . OrderPayment::class . ' is required as argument.'
            );
        }
    }

}
