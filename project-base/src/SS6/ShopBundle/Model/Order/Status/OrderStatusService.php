<?php

namespace SS6\ShopBundle\Model\Order\Status;

use SS6\ShopBundle\Model\Order\Status\OrderStatus;
use SS6\ShopBundle\Model\Order\Status\OrderStatusRepository;

class OrderStatusService {

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
