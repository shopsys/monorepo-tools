<?php

namespace Shopsys\FrameworkBundle\Model\Order\Item;

class OrderItemDataFactory implements OrderItemDataFactoryInterface
{
    /**
     * @return \Shopsys\FrameworkBundle\Model\Order\Item\OrderItemData
     */
    public function create(): OrderItemData
    {
        return new OrderItemData();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\OrderItem $orderItem
     * @return \Shopsys\FrameworkBundle\Model\Order\Item\OrderItemData
     */
    public function createFromOrderItem(OrderItem $orderItem): OrderItemData
    {
        $orderItemData = new OrderItemData();
        $this->fillFromOrderItem($orderItemData, $orderItem);

        return $orderItemData;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\OrderItemData $orderItemData
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\OrderItem $orderItem
     */
    protected function fillFromOrderItem(OrderItemData $orderItemData, OrderItem $orderItem)
    {
        $orderItemData->name = $orderItem->getName();
        $orderItemData->priceWithVat = $orderItem->getPriceWithVat();
        $orderItemData->priceWithoutVat = $orderItem->getPriceWithoutVat();
        $orderItemData->vatPercent = $orderItem->getVatPercent();
        $orderItemData->quantity = $orderItem->getQuantity();
        $orderItemData->unitName = $orderItem->getUnitName();
        $orderItemData->catnum = $orderItem->getCatnum();
    }
}
