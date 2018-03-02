<?php

namespace Shopsys\FrameworkBundle\Model\Order\Status\Exception;

use Exception;
use Shopsys\FrameworkBundle\Model\Order\Status\OrderStatus;

class OrderStatusDeletionForbiddenException extends Exception implements OrderStatusException
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\Status\OrderStatus
     */
    private $orderStatus;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Status\OrderStatus $orderStatus
     * @param \Exception|null $previous
     */
    public function __construct(OrderStatus $orderStatus, Exception $previous = null)
    {
        $this->orderStatus = $orderStatus;
        parent::__construct('Deletion of order status ID = ' . $orderStatus->getId() . ' is forbidden', 0, $previous);
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Order\Status\OrderStatus
     */
    public function getOrderStatus()
    {
        return $this->orderStatus;
    }
}
