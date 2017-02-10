<?php

namespace Shopsys\ShopBundle\Tests\Unit\Model\Order;

use PHPUnit_Framework_TestCase;
use Shopsys\ShopBundle\Model\Order\Item\OrderItemData;
use Shopsys\ShopBundle\Model\Order\OrderData;

class OrderDataTest extends PHPUnit_Framework_TestCase
{
    public function testGetNewItemsWithoutTransportAndPayment() {
        $orderData = new OrderData();
        $newOrderItemData = new OrderItemData();
        $oldOrderItemData = new OrderItemData();
        $items = [
            OrderData::NEW_ITEM_PREFIX . '1' => $newOrderItemData,
            1 => $oldOrderItemData,
        ];
        $orderData->itemsWithoutTransportAndPayment = $items;

        $newItems = $orderData->getNewItemsWithoutTransportAndPayment();

        $this->assertSame([$newOrderItemData], $newItems);
    }
}
