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
	 * @param \SS6\ShopBundle\Model\Order\Status\OrderStatus $oldOrderStatus
	 * @param \SS6\ShopBundle\Model\Order\Order[] $ordersWithOldStatus
	 * @param \SS6\ShopBundle\Model\Order\Status\OrderStatus|null $newOrderStatus
	 * @throws Exception\OrderStatusDeletionForbiddenException
	 */
	public function delete(OrderStatus $oldOrderStatus, array $ordersWithOldStatus, OrderStatus $newOrderStatus = null) {
		if ($oldOrderStatus->getType() !== OrderStatus::TYPE_IN_PROGRESS) {
			throw new \SS6\ShopBundle\Model\Order\Status\Exception\OrderStatusDeletionForbiddenException($oldOrderStatus);
		}

		if (count($ordersWithOldStatus) > 0) {
			if ($newOrderStatus === null) {
				throw new \SS6\ShopBundle\Model\Order\Status\Exception\OrderStatusDeletionWithOrdersException($oldOrderStatus);
			}

			$this->changeOrdersStatus($ordersWithOldStatus, $newOrderStatus);
		}
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Order[] $orders
	 * @param \SS6\ShopBundle\Model\Order\Status\OrderStatus $newOrderStatus
	 */
	public function changeOrdersStatus(array $orders, OrderStatus $newOrderStatus) {
		foreach ($orders as $order) {
			$order->setStatus($newOrderStatus);
		}
	}
}
