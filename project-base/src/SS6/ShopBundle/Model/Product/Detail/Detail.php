<?php

namespace SS6\ShopBundle\Model\Product\Detail;

use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\Price;

class Detail {

	/**
	 * @var \SS6\ShopBundle\Model\Product\Product
	 */
	private $product;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Price
	 */
	private $price;

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param \SS6\ShopBundle\Model\Product\Price $price
	 */
	public function __construct(Product $product, Price $price) {
		$this->product = $product;
		$this->price = $price;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\Product
	 */
	public function getProduct() {
		return $this->product;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\Price
	 */
	public function getPrice() {
		return $this->price;
	}

}
