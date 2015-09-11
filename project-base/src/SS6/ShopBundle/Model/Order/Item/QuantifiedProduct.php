<?php

namespace SS6\ShopBundle\Model\Order\Item;

use SS6\ShopBundle\Model\Product\Product;

class QuantifiedProduct {

	/**
	 * @var \SS6\ShopBundle\Model\Product\Product
	 */
	private $product;

	/**
	 * @var int
	 */
	private $quantity;

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param int $quantity
	 */
	public function __construct(Product $product, $quantity) {
		$this->product = $product;
		$this->quantity = $quantity;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\Product $product
	 */
	public function getProduct() {
		return $this->product;
	}

	/**
	 * @return int
	 */
	public function getQuantity() {
		return $this->quantity;
	}

}
