<?php

namespace Tests\ShopBundle\Functional\EntityExtension\Model;

use Doctrine\ORM\Mapping as ORM;
use Shopsys\FrameworkBundle\Model\Pricing\Price;
use Shopsys\ShopBundle\Model\Order\Order;
use Shopsys\ShopBundle\Model\Transport\Transport;

/**
 * @ORM\Entity
 *
 * Base of this class is a copy of OrderTransport entity from FrameworkBundle
 * Reason is described in /project-base/docs/wip_glassbox/entity-extension.md
 *
 * @see \Shopsys\FrameworkBundle\Model\Order\Item\OrderTransport
 */
class ExtendedOrderTransport extends ExtendedOrderItem
{
    /**
     * @var string|null
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $transportStringField;

    /**
     * @param \Shopsys\ShopBundle\Model\Order\Order $order
     * @param string $name
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Price $price
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
            ExtendedOrderItem::TYPE_TRANSPORT,
            null,
            null
        );
        $this->transport = $transport;
    }

    /**
     * @return string|null
     */
    public function getTransportStringField()
    {
        return $this->transportStringField;
    }

    /**
     * @param string|null $transportStringField
     */
    public function setTransportStringField($transportStringField)
    {
        $this->transportStringField = $transportStringField;
    }
}
