<?php

namespace Shopsys\ShopBundle\Model\Order\Status;

class OrderStatusService
{
    /**
     * @param \Shopsys\ShopBundle\Model\Order\Status\OrderStatus $oldOrderStatus
     */
    public function checkForDelete(OrderStatus $oldOrderStatus)
    {
        if ($oldOrderStatus->getType() !== OrderStatus::TYPE_IN_PROGRESS) {
            throw new \Shopsys\ShopBundle\Model\Order\Status\Exception\OrderStatusDeletionForbiddenException($oldOrderStatus);
        }
    }
}
