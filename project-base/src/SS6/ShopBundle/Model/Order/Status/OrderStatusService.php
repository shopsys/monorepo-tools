<?php

namespace SS6\ShopBundle\Model\Order\Status;

use SS6\ShopBundle\Model\Order\Status\OrderStatus;
use SS6\ShopBundle\Model\Order\Status\OrderStatusRepository;

class OrderStatusService {

	/**
	 * @param string $name
	 * @return \SS6\ShopBundle\Model\Order\Status\OrderStatus
	 */
	public function create($name) {
		$orderStatus = new OrderStatus($name);

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
	 * @throws Exception\DeletionForbiddenOrderStatusException
	 */
	public function delete(OrderStatus $orderStatus) {
		if ($orderStatus->getId() === OrderStatusRepository::STATUS_NEW) {
			throw new Exception\DeletionForbiddenOrderStatusException($orderStatus);
		}
	}
}
