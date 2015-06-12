<?php

namespace SS6\ShopBundle\Model\Product;

use SS6\ShopBundle\Model\Pricing\BasePriceCalculation;
use SS6\ShopBundle\Model\Pricing\InputPriceCalculation;
use SS6\ShopBundle\Model\Pricing\PricingSetting;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;
use SS6\ShopBundle\Model\Product\Pricing\ProductPriceCalculation;
use SS6\ShopBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler;
use SS6\ShopBundle\Model\Product\Pricing\ProductSellingPrice;
use SS6\ShopBundle\Model\Product\Product;

class ProductService {

	/**
	 * @var \SS6\ShopBundle\Model\Product\Pricing\ProductPriceCalculation
	 */
	private $productPriceCalculation;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\InputPriceCalculation
	 */
	private $inputPriceCalculation;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\BasePriceCalculation
	 */
	private $basePriceCalculation;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\PricingSetting
	 */
	private $pricingSetting;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler
	 */
	private $productPriceRecalculationScheduler;

	public function __construct(
		ProductPriceCalculation $productPriceCalculation,
		InputPriceCalculation $inputPriceCalculation,
		BasePriceCalculation $basePriceCalculation,
		PricingSetting $pricingSetting,
		ProductPriceRecalculationScheduler $productPriceRecalculationScheduler
	) {
		$this->productPriceCalculation = $productPriceCalculation;
		$this->inputPriceCalculation = $inputPriceCalculation;
		$this->basePriceCalculation = $basePriceCalculation;
		$this->pricingSetting = $pricingSetting;
		$this->productPriceRecalculationScheduler = $productPriceRecalculationScheduler;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param \SS6\ShopBundle\Model\Product\Pricing\ProductManualInputPrice[] $productManualInputPrices
	 * @param string $newVatPercent
	 */
	public function recalculateInputPriceForNewVatPercent(Product $product, $productManualInputPrices, $newVatPercent) {
		$inputPriceType = $this->pricingSetting->getInputPriceType();

		foreach ($productManualInputPrices as $productManualInputPrice) {
			$basePriceForPricingGroup = $this->basePriceCalculation->calculateBasePrice(
				$productManualInputPrice->getInputPrice(),
				$inputPriceType,
				$product->getVat()
			);
			$inputPriceForPricingGroup = $this->inputPriceCalculation->getInputPrice(
				$inputPriceType,
				$basePriceForPricingGroup->getPriceWithVat(),
				$newVatPercent
			);
			$productManualInputPrice->setInputPrice($inputPriceForPricingGroup);
		}

		$productPrice = $this->productPriceCalculation->calculateBasePrice($product);
		$inputPrice = $this->inputPriceCalculation->getInputPrice(
			$inputPriceType,
			$productPrice->getPriceWithVat(),
			$newVatPercent
		);

		$this->setInputPrice($product, $inputPrice);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param \SS6\ShopBundle\Model\Product\ProductData $productData
	 */
	public function edit(Product $product, ProductData $productData) {
		$product->edit($productData);
		$this->productPriceRecalculationScheduler->scheduleRecalculatePriceForProduct($product);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param string $inputPrice
	 */
	public function setInputPrice(Product $product, $inputPrice) {
		$product->setPrice($inputPrice);
		$this->productPriceRecalculationScheduler->scheduleRecalculatePriceForProduct($product);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param \SS6\ShopBundle\Model\Pricing\Vat\Vat $vat
	 */
	public function changeVat(Product $product, Vat $vat) {
		$product->changeVat($vat);
		$this->productPriceRecalculationScheduler->scheduleRecalculatePriceForProduct($product);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup[] $pricingGroups
	 * @return \SS6\ShopBundle\Model\Product\Pricing\ProductSellingPrice[]
	 */
	public function getProductSellingPricesIndexedByDomainIdAndPricingGroupId(Product $product, array $pricingGroups) {
		$productSellingPrices = [];
		foreach ($pricingGroups as $pricingGroup) {
			$productSellingPrices[$pricingGroup->getDomainId()][$pricingGroup->getId()] = new ProductSellingPrice(
				$pricingGroup,
				$this->productPriceCalculation->calculatePrice($product, $pricingGroup)
			);
		}

		return $productSellingPrices;
	}

}
