<?php

namespace SS6\ShopBundle\Model\Product\Pricing;

use SS6\ShopBundle\Model\Pricing\BasePriceCalculation;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroup;
use SS6\ShopBundle\Model\Pricing\PricingSetting;
use SS6\ShopBundle\Model\Product\Pricing\ProductInputPriceRepository;
use SS6\ShopBundle\Model\Product\Product;

class ProductPriceCalculation {

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\BasePriceCalculation
	 */
	private $basePriceCalculation;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\PricingSetting
	 */
	private $pricingSetting;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Pricing\ProductInputPriceRepository
	 */
	private $productInputPriceRepository;

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\BasePriceCalculation $basePriceCalculation
	 * @param \SS6\ShopBundle\Model\Pricing\PricingSetting $pricingSetting
	 * @param \SS6\ShopBundle\Model\Product\Pricing\ProductInputPriceRepository $productInputPriceRepository
	 */
	public function __construct(
		BasePriceCalculation $basePriceCalculation,
		PricingSetting $pricingSetting,
		ProductInputPriceRepository $productInputPriceRepository
	) {
		$this->pricingSetting = $pricingSetting;
		$this->basePriceCalculation = $basePriceCalculation;
		$this->productInputPriceRepository = $productInputPriceRepository;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 * @return \SS6\ShopBundle\Model\Pricing\Price
	 */
	public function calculatePrice(Product $product, PricingGroup $pricingGroup) {
		if ($product->getPriceCalculationType() === Product::PRICE_CALCULATION_TYPE_AUTO) {
			$basePrice = $this->calculateBasePrice($product);

			return $this->basePriceCalculation->applyCoefficient(
				$basePrice,
				$product->getVat(),
				$pricingGroup->getCoefficient()
			);
		} else {
			return $this->calculateBasePriceForPricingGroup($product, $pricingGroup);
		}
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @return \SS6\ShopBundle\Model\Pricing\Price
	 */
	public function calculateBasePrice(Product $product) {
		return $this->basePriceCalculation->calculatePrice(
				$product->getPrice(),
				$this->pricingSetting->getInputPriceType(),
				$product->getVat()
			);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 * @return \SS6\ShopBundle\Model\Pricing\Price
	 */
	public function calculateBasePriceForPricingGroup(Product $product, PricingGroup $pricingGroup) {
		$productInputPrice = $this->productInputPriceRepository->findByProductAndPricingGroup($product, $pricingGroup);
		if ($productInputPrice !== null) {
			$price = $productInputPrice->getInputPrice();
		} else {
			$price = 0;
		}
		return $this->basePriceCalculation->calculatePrice(
			$price,
			$this->pricingSetting->getInputPriceType(),
			$product->getVat()
		);
	}

}
