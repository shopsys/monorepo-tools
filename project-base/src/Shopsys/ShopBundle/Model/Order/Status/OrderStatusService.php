<?php

namespace SS6\ShopBundle\Model\Order\Status;

use SS6\ShopBundle\Model\Order\Status\OrderStatus;

class OrderStatusService {

	/**
	 * @param \SS6\ShopBundle\Model\Order\Status\OrderStatus $oldOrderStatus
	 */
	public function checkForDelete(OrderStatus $oldOrderStatus) {
		if ($oldOrderStatus->getType() !== OrderStatus::TYPE_IN_PROGRESS) {
			throw new \SS6\ShopBundle\Model\Order\Status\Exception\OrderStatusDeletionForbiddenException($oldOrderStatus);
		}
	}
}
