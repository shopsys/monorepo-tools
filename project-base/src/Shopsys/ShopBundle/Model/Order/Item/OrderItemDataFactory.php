<?php

declare(strict_types=1);

namespace Shopsys\ShopBundle\Model\Order\Item;

use Shopsys\FrameworkBundle\Model\Order\Item\OrderItem as BaseOrderItem;
use Shopsys\FrameworkBundle\Model\Order\Item\OrderItemData as BaseOrderItemData;
use Shopsys\FrameworkBundle\Model\Order\Item\OrderItemDataFactory as BaseOrderItemDataFactory;

class OrderItemDataFactory extends BaseOrderItemDataFactory
{
    /**
     * @return \Shopsys\ShopBundle\Model\Order\Item\OrderItemData
     */
    public function create(): BaseOrderItemData
    {
        return new OrderItemData();
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Order\Item\OrderItem $orderItem
     * @return \Shopsys\ShopBundle\Model\Order\Item\OrderItemData
     */
    public function createFromOrderItem(BaseOrderItem $orderItem): BaseOrderItemData
    {
        $orderItemData = new OrderItemData();
        $this->fillFromOrderItem($orderItemData, $orderItem);
        $this->addFieldsByOrderItemType($orderItemData, $orderItem);

        return $orderItemData;
    }
}
