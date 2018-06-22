<?php

namespace Shopsys\FrameworkBundle\Model\Order\Item;

class OrderTransportData extends OrderItemData
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Transport\Transport|null
     */
    public $transport;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\OrderItem $orderTransport
     */
    public function setFromEntity(OrderItem $orderTransport)
    {
        if ($orderTransport instanceof OrderTransport) {
            $this->transport = $orderTransport->getTransport();
            parent::setFromEntity($orderTransport);
        } else {
            throw new \Shopsys\FrameworkBundle\Model\Order\Item\Exception\InvalidArgumentException(
                'Instance of ' . OrderTransport::class . ' is required as argument.'
            );
        }
    }
}
