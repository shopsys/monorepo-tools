<?php

namespace Shopsys\FrameworkBundle\Model\Order\Item;

use Doctrine\ORM\Mapping as ORM;
use Shopsys\FrameworkBundle\Model\Order\Order;
use Shopsys\FrameworkBundle\Model\Pricing\Price;
use Shopsys\FrameworkBundle\Model\Transport\Transport;

/**
 * @ORM\Entity
 */
class OrderTransport extends OrderItem
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Transport\Transport
     *
     * @ORM\ManyToOne(targetEntity="Shopsys\FrameworkBundle\Model\Transport\Transport")
     * @ORM\JoinColumn(nullable=true)
     */
    private $transport;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Order $order
     * @param string $name
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Price $price
     * @param string $vatPercent
     * @param int $quantity
     * @param \Shopsys\FrameworkBundle\Model\Transport\Transport $transport
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
     * @return \Shopsys\FrameworkBundle\Model\Transport\Transport
     */
    public function getTransport()
    {
        return $this->transport;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\OrderItemData $orderTransportData
     */
    public function edit(OrderItemData $orderTransportData)
    {
        if ($orderTransportData instanceof OrderTransportData) {
            $this->transport = $orderTransportData->transport;
            parent::edit($orderTransportData);
        } else {
            throw new \Shopsys\FrameworkBundle\Model\Order\Item\Exception\InvalidArgumentException(
                'Instance of ' . OrderTransportData::class . ' is required as argument.'
            );
        }
    }
}
