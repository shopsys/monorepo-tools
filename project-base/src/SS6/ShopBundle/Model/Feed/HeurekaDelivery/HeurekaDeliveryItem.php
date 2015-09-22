<?php

namespace SS6\ShopBundle\Model\Feed\HeurekaDelivery;

class HeurekaDeliveryItem {

	/**
	 * @var int
	 */
	private $itemId;

	/**
	 * @var int
	 */
	private $stockQuantity;

	/**
	 * @param int $itemId
	 * @param int $stockQuantity
	 */
	public function __construct($itemId, $stockQuantity) {
		$this->itemId = $itemId;
		$this->stockQuantity = $stockQuantity;
	}

	/**
	 * @return int
	 */
	public function getItemId() {
		return $this->itemId;
	}

	/**
	 * @return int
	 */
	public function getStockQuantity() {
		return $this->stockQuantity;
	}

}
