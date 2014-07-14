<?php

namespace SS6\ShopBundle\Model\Order\Review;

class OrderReviewItem {

	const TYPE_PAYMENT = 'payment';
	const TYPE_PRODUCT = 'product';
	const TYPE_TRANSPORT = 'transport';

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var string
	 */
	private $type;

	/**
	 * @var int
	 */
	private $quantity;

	/**
	 * @var string
	 */
	private $price;

	/**
	 * @param string $name
	 * @param strng $type
	 * @param int $quantity
	 * @param string $price
	 */
	public function __construct($name, $type, $quantity, $price) {
		$this->name = $name;
		$this->type = $type;
		$this->quantity = $quantity;
		$this->price = $price;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * @return int
	 */
	public function getQuantity() {
		return $this->quantity;
	}

	/**
	 * @return string
	 */
	public function getPrice() {
		return $this->price;
	}
}
