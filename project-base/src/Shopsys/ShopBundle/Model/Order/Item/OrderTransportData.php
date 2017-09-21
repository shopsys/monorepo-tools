<?php

namespace Shopsys\ShopBundle\Model\Order\Item;

class OrderTransportData extends OrderItemData
{
    /**
     * @var \Shopsys\ShopBundle\Model\Transport\Transport
     */
    public $transport;

    /**
     * @param \Shopsys\ShopBundle\Model\Order\Item\OrderItem $orderTransport
     */
    public function setFromEntity(OrderItem $orderTransport)
    {
        if ($orderTransport instanceof OrderTransport) {
            $this->transport = $orderTransport->getTransport();
            parent::setFromEntity($orderTransport);
        } else {
            throw new \Shopsys\ShopBundle\Model\Order\Item\Exception\InvalidArgumentException(
                'Instance of ' . OrderTransport::class . ' is required as argument.'
            );
        }
    }
}
