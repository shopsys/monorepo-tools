<?php

namespace Shopsys\FrameworkBundle\Model\Order\Status;

class OrderStatusService
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Status\OrderStatus $oldOrderStatus
     */
    public function checkForDelete(OrderStatus $oldOrderStatus)
    {
        if ($oldOrderStatus->getType() !== OrderStatus::TYPE_IN_PROGRESS) {
            throw new \Shopsys\FrameworkBundle\Model\Order\Status\Exception\OrderStatusDeletionForbiddenException($oldOrderStatus);
        }
    }
}
