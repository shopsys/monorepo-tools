<?php

namespace SS6\ShopBundle\Model\Product\Detail;

use SS6\ShopBundle\Model\Product\PriceCalculation;
use SS6\ShopBundle\Model\Product\Product;

class Factory {

	/**
	 * @var \SS6\ShopBundle\Model\Product\PriceCalculation
	 */
	private $priceCalculation;

	/**
	 * @param \SS6\ShopBundle\Model\Product\PriceCalculation $priceCalculation
	 */
	public function __construct(PriceCalculation $priceCalculation) {
		$this->priceCalculation = $priceCalculation;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @return \SS6\ShopBundle\Model\Product\Detail\Detail
	 */
	public function getDetailForProduct(Product $product) {
		return new Detail(
			$product,
			$this->getPrice($product)
		);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product[] $products
	 * @return \SS6\ShopBundle\Model\Product\Detail\Detail[]
	 */
	public function getDetailsForProducts(array $products) {
		$details = array();

		foreach ($products as $product) {
			$details[] = new Detail(
				$product,
				$this->getPrice($product)
			);
		}

		return $details;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @return \SS6\ShopBundle\Model\Pricing\Price
	 */
	private function getPrice(Product $product) {
		return $this->priceCalculation->calculatePrice($product);
	}

}
