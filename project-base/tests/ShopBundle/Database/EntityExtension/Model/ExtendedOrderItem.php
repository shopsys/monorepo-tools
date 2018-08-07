<?php

namespace Tests\ShopBundle\Database\EntityExtension\Model;

use Doctrine\ORM\Mapping as ORM;
use Shopsys\FrameworkBundle\Model\Order\Item\OrderItem;

/**
 * @ORM\Entity
 * @ORM\Table(name="order_items")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({
 *     "payment" = "\Tests\ShopBundle\Database\EntityExtension\Model\ExtendedOrderPayment",
 *     "product" = "\Tests\ShopBundle\Database\EntityExtension\Model\ExtendedOrderProduct",
 *     "transport" = "\Tests\ShopBundle\Database\EntityExtension\Model\ExtendedOrderTransport"
 * })
 */
abstract class ExtendedOrderItem extends OrderItem
{
    /**
     * @var string|null
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $stringField;

    /**
     * @return string|null
     */
    public function getStringField()
    {
        return $this->stringField;
    }

    /**
     * @param string|null $stringField
     */
    public function setStringField($stringField)
    {
        $this->stringField = $stringField;
    }
}
