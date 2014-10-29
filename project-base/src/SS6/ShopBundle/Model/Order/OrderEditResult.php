<?php

namespace SS6\ShopBundle\Model\Order;

class OrderEditResult {
	/**
	 * @var \SS6\ShopBundle\Model\Order\Item\OrderItem[]
	 */
	private $orderItemsToDelete;

	/**
	 * @param \SS6\ShopBundle\Model\Order\Item\OrderItem[] $orderItemsToDelete
	 */
	public function __construct(array $orderItemsToDelete) {
		$this->orderItemsToDelete = $orderItemsToDelete;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Order\Item\OrderItem[]
	 */
	public function getOrderItemsToDelete() {
		return $this->orderItemsToDelete;
	}

}
