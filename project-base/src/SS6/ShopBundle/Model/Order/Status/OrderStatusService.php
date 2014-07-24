<?php

namespace SS6\ShopBundle\Model\Order\Status;

use SS6\ShopBundle\Model\Order\Status\OrderStatus;

class OrderStatusService {

	/**
	 * @param string $name
	 * @param int $type
	 * @return \SS6\ShopBundle\Model\Order\Status\OrderStatus
	 */
	public function create($name, $type) {
		$orderStatus = new OrderStatus($name, $type);

		return $orderStatus;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Status\OrderStatus $orderStatus
	 * @param string $name
	 * @return \SS6\ShopBundle\Model\Order\Status\OrderStatus
	 */
	public function edit(OrderStatus $orderStatus, $name) {
		$orderStatus->edit($name);
		return $orderStatus;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Status\OrderStatus $orderStatus
	 * @param int $ordersCountByStatus
	 * @throws Exception\OrderStatusDeletionForbiddenException
	 */
	public function delete(OrderStatus $orderStatus, $ordersCountByStatus) {
		if ($orderStatus->getType() !== OrderStatus::TYPE_IN_PROGRESS) {
			throw new \SS6\ShopBundle\Model\Order\Status\Exception\OrderStatusDeletionForbiddenException($orderStatus);
		}
		if ($ordersCountByStatus > 0) {
			throw new \SS6\ShopBundle\Model\Order\Status\Exception\OrderStatusDeletionWithOrdersException($orderStatus);
		}
	}
}
