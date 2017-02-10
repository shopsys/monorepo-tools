<?php

namespace Shopsys\ShopBundle\Model\Order\Item;

use Doctrine\ORM\Mapping as ORM;
use Shopsys\ShopBundle\Model\Order\Item\OrderItem;
use Shopsys\ShopBundle\Model\Order\Item\OrderItemData;
use Shopsys\ShopBundle\Model\Order\Item\OrderTransportData;
use Shopsys\ShopBundle\Model\Order\Order;
use Shopsys\ShopBundle\Model\Pricing\Price;
use Shopsys\ShopBundle\Model\Transport\Transport;

/**
 * @ORM\Entity
 */
class OrderTransport extends OrderItem {

    /**
     * @var \Shopsys\ShopBundle\Model\Transport\Transport
     *
     * @ORM\ManyToOne(targetEntity="Shopsys\ShopBundle\Model\Transport\Transport")
     * @ORM\JoinColumn(nullable=true)
     */
    private $transport;

    /**
     * @param \Shopsys\ShopBundle\Model\Order\Order $order
     * @param string $name
     * @param \Shopsys\ShopBundle\Model\Pricing\Price $price
     * @param string $vatPercent
     * @param int $quantity
     * @param \Shopsys\ShopBundle\Model\Transport\Transport $transport
     */
    public function __construct(
        Order $order,
        $name,
        Price $price,
        $vatPercent,
        $quantity,
        Transport $transport
    ) {
        parent::__construct(
            $order,
            $name,
            $price,
            $vatPercent,
            $quantity,
            null,
            null
        );
        $this->transport = $transport;
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Transport\Transport
     */
    public function getTransport() {
        return $this->transport;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Order\Item\OrderItemData $orderTransportData
     */
    public function edit(OrderItemData $orderTransportData) {
        if ($orderTransportData instanceof OrderTransportData) {
            $this->transport = $orderTransportData->transport;
            parent::edit($orderTransportData);
        } else {
            throw new \Shopsys\ShopBundle\Model\Order\Item\Exception\InvalidArgumentException(
                'Instance of ' . OrderTransportData::class . ' is required as argument.'
            );
        }
    }

}
