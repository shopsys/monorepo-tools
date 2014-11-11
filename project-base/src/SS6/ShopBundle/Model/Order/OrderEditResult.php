<?php

namespace SS6\ShopBundle\Model\Order;

class OrderEditResult {

	/**
	 * @var \SS6\ShopBundle\Model\Order\Item\OrderItem[]
	 */
	private $orderItemsToCreate;

	/**
	 * @var \SS6\ShopBundle\Model\Order\Item\OrderItem[]
	 */
	private $orderItemsToDelete;

	/**
	 * @param \SS6\ShopBundle\Model\Order\Item\OrderItem[] $orderItemsToCreate
	 * @param \SS6\ShopBundle\Model\Order\Item\OrderItem[] $orderItemsToDelete
	 */
	public function __construct(array $orderItemsToCreate, array $orderItemsToDelete) {
		$this->orderItemsToCreate = $orderItemsToCreate;
		$this->orderItemsToDelete = $orderItemsToDelete;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Order\Item\OrderItem[]
	 */
	public function getOrderItemsToCreate() {
		return $this->orderItemsToCreate;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Order\Item\OrderItem[]
	 */
	public function getOrderItemsToDelete() {
		return $this->orderItemsToDelete;
	}

}
