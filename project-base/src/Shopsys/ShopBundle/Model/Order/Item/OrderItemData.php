<?php

namespace Shopsys\ShopBundle\Model\Order\Item;

class OrderItemData
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $priceWithVat;

    /**
     * @var string
     */
    public $priceWithoutVat;

    /**
     * @var string
     */
    public $vatPercent;

    /**
     * @var int
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
     * @param \Shopsys\ShopBundle\Model\Order\Item\OrderItem $orderItem
     */
    public function setFromEntity(OrderItem $orderItem) {
        $this->name = $orderItem->getName();
        $this->priceWithVat = $orderItem->getPriceWithVat();
        $this->priceWithoutVat = $orderItem->getPriceWithoutVat();
        $this->vatPercent = $orderItem->getVatPercent();
        $this->quantity = $orderItem->getQuantity();
        $this->unitName = $orderItem->getUnitName();
        $this->catnum = $orderItem->getCatnum();
    }
}
