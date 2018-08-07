<?php

namespace Shopsys\FrameworkBundle\Model\Order\Status;

class OrderStatusFactory implements OrderStatusFactoryInterface
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Status\OrderStatusData $data
     * @param int $type
     * @return \Shopsys\FrameworkBundle\Model\Order\Status\OrderStatus
     */
    public function create(OrderStatusData $data, int $type): OrderStatus
    {
        return new OrderStatus($data, $type);
    }
}
