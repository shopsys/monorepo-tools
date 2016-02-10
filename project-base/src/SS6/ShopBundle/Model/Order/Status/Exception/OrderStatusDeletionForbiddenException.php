<?php

namespace SS6\ShopBundle\Model\Order\Status\Exception;

use Exception;
use SS6\ShopBundle\Model\Order\Status\OrderStatus;

class OrderStatusDeletionForbiddenException extends Exception implements OrderStatusException {

	/**
	 * @var \SS6\ShopBundle\Model\Order\Status\OrderStatus
	 */
	private $orderStatus;

	/**
	 * @param \SS6\ShopBundle\Model\Order\Status\OrderStatus $orderStatus
	 * @param \Exception|null $previous
	 */
	public function __construct(OrderStatus $orderStatus, Exception $previous = null) {
		$this->orderStatus = $orderStatus;
		parent::__construct('Deletion of order status ID = ' . $orderStatus->getId() . ' is forbidden', 0, $previous);
	}

	/**
	 * @return \SS6\ShopBundle\Model\Order\Status\OrderStatus
	 */
	public function getOrderStatus() {
		return $this->orderStatus;
	}

}
