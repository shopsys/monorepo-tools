<?php

namespace Shopsys\ShopBundle\Model\Order\Status\Exception;

use Exception;
use Shopsys\ShopBundle\Model\Order\Status\OrderStatus;

class OrderStatusDeletionForbiddenException extends Exception implements OrderStatusException {

    /**
     * @var \Shopsys\ShopBundle\Model\Order\Status\OrderStatus
     */
    private $orderStatus;

    /**
     * @param \Shopsys\ShopBundle\Model\Order\Status\OrderStatus $orderStatus
     * @param \Exception|null $previous
     */
    public function __construct(OrderStatus $orderStatus, Exception $previous = null) {
        $this->orderStatus = $orderStatus;
        parent::__construct('Deletion of order status ID = ' . $orderStatus->getId() . ' is forbidden', 0, $previous);
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Order\Status\OrderStatus
     */
    public function getOrderStatus() {
        return $this->orderStatus;
    }

}
