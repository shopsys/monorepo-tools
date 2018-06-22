<?php

namespace Shopsys\FrameworkBundle\Model\Order\Item;

class OrderItemData
{
    /**
     * @var string|null
     */
    public $name;

    /**
     * @var string|null
     */
    public $priceWithVat;

    /**
     * @var string|null
     */
    public $priceWithoutVat;

    /**
     * @var string|null
     */
    public $vatPercent;

    /**
     * @var int|null
     */
    public $quantity;

    /**
     * @var string|null
     */
    public $unitName;

    /**
     * @var string|null
     */
    public $catnum;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\OrderItem $orderItem
     */
    public function setFromEntity(OrderItem $orderItem)
    {
        $this->name = $orderItem->getName();
        $this->priceWithVat = $orderItem->getPriceWithVat();
        $this->priceWithoutVat = $orderItem->getPriceWithoutVat();
        $this->vatPercent = $orderItem->getVatPercent();
        $this->quantity = $orderItem->getQuantity();
        $this->unitName = $orderItem->getUnitName();
        $this->catnum = $orderItem->getCatnum();
    }
}
