<?php

declare(strict_types=1);

namespace Tests\ShopBundle\Functional\EntityExtension\Model;

use Doctrine\ORM\Mapping as ORM;
use Shopsys\FrameworkBundle\Model\Order\Item\OrderItem;

/**
 * @ORM\Entity
 * @ORM\Table(name="order_items")
 */
class ExtendedOrderItem extends OrderItem
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
