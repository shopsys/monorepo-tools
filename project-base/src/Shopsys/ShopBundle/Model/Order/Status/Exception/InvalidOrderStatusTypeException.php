<?php

namespace Shopsys\ShopBundle\Model\Order\Status\Exception;

use Exception;

class InvalidOrderStatusTypeException extends Exception implements OrderStatusException {

    /**
     * @var int
     */
    private $orderStatusType;

    /**
     * @param int $orderStatusType
     * @param \Exception|null $previous
     */
    public function __construct($orderStatusType, Exception $previous = null) {
        $this->orderStatusType = $orderStatusType;
        parent::__construct('Order status type ' . $orderStatusType . ' is not valid', 0, $previous);
    }

    /**
     * @return int
     */
    public function getOrderStatusType() {
        return $this->orderStatusType;
    }

}
