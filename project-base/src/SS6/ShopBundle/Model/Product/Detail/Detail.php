<?php

namespace SS6\ShopBundle\Model\Product\Detail;

use SS6\ShopBundle\Model\Pricing\Price;
use SS6\ShopBundle\Model\Product\Product;

class Detail {

	/**
	 * @var \SS6\ShopBundle\Model\Product\Product
	 */
	private $product;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Price
	 */
	private $price;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Parameter\ProductParameterValue[]
	 */
	private $parameters;

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param \SS6\ShopBundle\Model\Pricing\Price $price
	 * @param \SS6\ShopBundle\Model\Product\Parameter\ProductParameterValue[] $parameters
	 */
	public function __construct(
		Product $product,
		Price $price,
		array $parameters
	) {
		$this->product = $product;
		$this->price = $price;
		$this->parameters = $parameters;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\Product
	 */
	public function getProduct() {
		return $this->product;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Pricing\Price
	 */
	public function getPrice() {
		return $this->price;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\Parameter\ProductParameterValue[]
	 */
	public function getParameters() {
		return $this->parameters;
	}

}
