<?php

namespace Shopsys\ShopBundle\Model\Product;

use Shopsys\ShopBundle\Model\Pricing\BasePriceCalculation;
use Shopsys\ShopBundle\Model\Pricing\InputPriceCalculation;
use Shopsys\ShopBundle\Model\Pricing\PricingSetting;
use Shopsys\ShopBundle\Model\Pricing\Vat\Vat;
use Shopsys\ShopBundle\Model\Product\Pricing\ProductPriceCalculation;
use Shopsys\ShopBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler;
use Shopsys\ShopBundle\Model\Product\Pricing\ProductSellingPrice;
use Shopsys\ShopBundle\Model\Product\Product;
use Shopsys\ShopBundle\Model\Product\ProductDeleteResult;

class ProductService {

	/**
	 * @var \Shopsys\ShopBundle\Model\Product\Pricing\ProductPriceCalculation
	 */
	private $productPriceCalculation;

	/**
	 * @var \Shopsys\ShopBundle\Model\Pricing\InputPriceCalculation
	 */
	private $inputPriceCalculation;

	/**
	 * @var \Shopsys\ShopBundle\Model\Pricing\BasePriceCalculation
	 */
	private $basePriceCalculation;

	/**
	 * @var \Shopsys\ShopBundle\Model\Pricing\PricingSetting
	 */
	private $pricingSetting;

	/**
	 * @var \Shopsys\ShopBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler
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
	 * @param \Shopsys\ShopBundle\Model\Product\Product $product
	 * @param \Shopsys\ShopBundle\Model\Product\Pricing\ProductManualInputPrice[] $productManualInputPrices
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

		$productBasePrice = $this->basePriceCalculation->calculateBasePrice(
			$product->getPrice(),
			$inputPriceType,
			$product->getVat()
		);
		$inputPrice = $this->inputPriceCalculation->getInputPrice(
			$inputPriceType,
			$productBasePrice->getPriceWithVat(),
			$newVatPercent
		);

		$this->setInputPrice($product, $inputPrice);
	}

	/**
	 * @param \Shopsys\ShopBundle\Model\Product\Product $product
	 * @param \Shopsys\ShopBundle\Model\Product\ProductData $productData
	 */
	public function edit(Product $product, ProductData $productData) {
		$product->edit($productData);
		$this->productPriceRecalculationScheduler->scheduleProductForImmediateRecalculation($product);
		$this->markProductForVisibilityRecalculation($product);
	}

	/**
	 * @param \Shopsys\ShopBundle\Model\Product\Product $product
	 * @param string $inputPrice
	 */
	public function setInputPrice(Product $product, $inputPrice) {
		$product->setPrice($inputPrice);
		$this->productPriceRecalculationScheduler->scheduleProductForImmediateRecalculation($product);
	}

	/**
	 * @param \Shopsys\ShopBundle\Model\Product\Product $product
	 * @param \Shopsys\ShopBundle\Model\Pricing\Vat\Vat $vat
	 */
	public function changeVat(Product $product, Vat $vat) {
		$product->changeVat($vat);
		$this->productPriceRecalculationScheduler->scheduleProductForImmediateRecalculation($product);
	}

	/**
	 * @param \Shopsys\ShopBundle\Model\Product\Product $product
	 * @param \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup[] $pricingGroups
	 * @return \Shopsys\ShopBundle\Model\Product\Pricing\ProductSellingPrice[]
	 */
	public function getProductSellingPricesIndexedByDomainIdAndPricingGroupId(Product $product, array $pricingGroups) {
		$productSellingPrices = [];
		foreach ($pricingGroups as $pricingGroup) {
			$productSellingPrices[$pricingGroup->getDomainId()][$pricingGroup->getId()] = new ProductSellingPrice(
				$pricingGroup,
				$this->productPriceCalculation->calculatePrice($product, $pricingGroup->getDomainId(), $pricingGroup)
			);
		}

		return $productSellingPrices;
	}

	/**
	 * @param \Shopsys\ShopBundle\Model\Product\Product $product
	 * @return \Shopsys\ShopBundle\Model\Product\ProductDeleteResult
	 */
	public function delete(Product $product) {
		if ($product->isMainVariant()) {
			foreach ($product->getVariants() as $variantProduct) {
				$variantProduct->unsetMainVariant();
			}
		}
		if ($product->isVariant()) {
			return new ProductDeleteResult([$product->getMainVariant()]);
		}

		return new ProductDeleteResult();
	}

	/**
	 * @param \Shopsys\ShopBundle\Model\Product\Product $product
	 */
	public function markProductForVisibilityRecalculation(Product $product) {
		$product->markForVisibilityRecalculation();
		if ($product->isMainVariant()) {
			foreach ($product->getVariants() as $variant) {
				$variant->markForVisibilityRecalculation();
			}
		} elseif ($product->isVariant()) {
			$product->getMainVariant()->markForVisibilityRecalculation();
		}
	}

	/**
	 * @param \Shopsys\ShopBundle\Model\Product\Product[] $products
	 * @param int[] $orderedProductIds
	 * @return \Shopsys\ShopBundle\Model\Product\Product[]
	 */
	public function sortProductsByProductIds(array $products, array $orderedProductIds) {
		$orderedProductIds = array_values($orderedProductIds);

		usort($products, function (Product $product1, Product $product2) use ($orderedProductIds) {
			$product1Priority = array_search($product1->getId(), $orderedProductIds, true);
			$product2Priority = array_search($product2->getId(), $orderedProductIds, true);

			return $product1Priority < $product2Priority ? -1 : 1;
		});

		return $products;
	}

}
