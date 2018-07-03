<?php

namespace Shopsys\FrameworkBundle\Model\Order\Item;

interface OrderItemDataFactoryInterface
{
    /**
     * @return \Shopsys\FrameworkBundle\Model\Order\Item\OrderItemData
     */
    public function create(): OrderItemData;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\OrderItem $orderItem
     * @return \Shopsys\FrameworkBundle\Model\Order\Item\OrderItemData
     */
    public function createFromOrderItem(OrderItem $orderItem): OrderItemData;
}
