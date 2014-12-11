<?php

namespace SS6\ShopBundle\Model\Order\Item;

class QuantifiedItem {

	/**
	 * @var object
	 */
	private $item;

	/**
	 * @var int
	 */
	private $quantity;

	/**
	 * @param object $item
	 * @param int $quantity
	 */
	public function __construct($item, $quantity) {
		$this->item = $item;
		$this->quantity = $quantity;
	}

	/**
	 * @return object
	 */
	public function getItem() {
		return $this->item;
	}

	/**
	 * @return int
	 */
	public function getQuantity() {
		return $this->quantity;
	}

}
