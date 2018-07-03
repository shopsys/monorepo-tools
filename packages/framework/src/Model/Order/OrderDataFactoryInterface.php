<?php

namespace Shopsys\FrameworkBundle\Model\Order;

interface OrderDataFactoryInterface
{
    /**
     * @return \Shopsys\FrameworkBundle\Model\Order\OrderData
     */
    public function create(): OrderData;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Order $order
     * @return \Shopsys\FrameworkBundle\Model\Order\OrderData
     */
    public function createFromOrder(Order $order): OrderData;
}
