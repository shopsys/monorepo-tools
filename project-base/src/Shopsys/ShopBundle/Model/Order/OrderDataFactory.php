<?php

namespace Shopsys\ShopBundle\Model\Order;

use Shopsys\FrameworkBundle\Model\Order\Order as BaseOrder;
use Shopsys\FrameworkBundle\Model\Order\OrderData as BaseOrderData;
use Shopsys\FrameworkBundle\Model\Order\OrderDataFactory as BaseOrderDataFactory;

class OrderDataFactory extends BaseOrderDataFactory
{
    /**
     * @return \Shopsys\ShopBundle\Model\Order\OrderData
     */
    public function create(): BaseOrderData
    {
        return new OrderData();
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Order\Order $order
     * @return \Shopsys\ShopBundle\Model\Order\OrderData
     */
    public function createFromOrder(BaseOrder $order): BaseOrderData
    {
        $orderData = new OrderData();
        $this->fillFromOrder($orderData, $order);

        return $orderData;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Order\OrderData $orderData
     * @param \Shopsys\ShopBundle\Model\Order\Order $order
     */
    protected function fillFromOrder(BaseOrderData $orderData, BaseOrder $order)
    {
        parent::fillFromOrder($orderData, $order);
    }
}
