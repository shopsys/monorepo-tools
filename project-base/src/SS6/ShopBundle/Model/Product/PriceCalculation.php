<?php

namespace SS6\ShopBundle\Model\Product;

use SS6\ShopBundle\Model\Pricing\PriceCalculation as GenericPriceCalculation;
use SS6\ShopBundle\Model\Pricing\PricingSetting;

class PriceCalculation {

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\PriceCalculation
	 */
	private $genericPriceCalculation;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\PricingSetting
	 */
	private $pricingSetting;

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\PriceCalculation $genericPriceCalculation
	 * @param \SS6\ShopBundle\Model\Pricing\PricingSetting $pricingSetting
	 */
	public function __construct(
		GenericPriceCalculation $genericPriceCalculation,
		PricingSetting $pricingSetting
	) {
		$this->pricingSetting = $pricingSetting;
		$this->genericPriceCalculation = $genericPriceCalculation;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @return \SS6\ShopBundle\Model\Pricing\Price
	 */
	public function calculatePrice(Product $product) {
		return $this->genericPriceCalculation->calculatePrice(
			$product->getPrice(),
			$this->pricingSetting->getInputPriceType(),
			$product->getVat()
		);
	}

}
