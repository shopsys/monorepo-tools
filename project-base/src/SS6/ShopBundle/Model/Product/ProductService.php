<?php

namespace SS6\ShopBundle\Model\Product;

use SS6\ShopBundle\Model\Pricing\InputPriceCalculation;
use SS6\ShopBundle\Model\Pricing\PricingSetting;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;
use SS6\ShopBundle\Model\Product\Pricing\ProductPriceCalculation;
use SS6\ShopBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler;
use SS6\ShopBundle\Model\Product\Pricing\ProductSellingPrice;
use SS6\ShopBundle\Model\Product\Parameter\ProductParameterValue;
use SS6\ShopBundle\Model\Product\Parameter\ProductParameterValueData;
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
		PricingSetting $pricingSetting,
		ProductPriceRecalculationScheduler $productPriceRecalculationScheduler
	) {
		$this->productPriceCalculation = $productPriceCalculation;
		$this->inputPriceCalculation = $inputPriceCalculation;
		$this->pricingSetting = $pricingSetting;
		$this->productPriceRecalculationScheduler = $productPriceRecalculationScheduler;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param \SS6\ShopBundle\Model\Pricing\Vat\Vat $newVat
	 */
	public function replaceOldVatWithNewVat(Product $product, Vat $newVat) {
		$this->recalculateInputPriceForNewVatPercent($product, $newVat->getPercent());
		$this->changeVat($product, $newVat);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param string $newVatPercent
	 */
	public function recalculateInputPriceForNewVatPercent(Product $product, $newVatPercent) {
		$productPrice = $this->productPriceCalculation->calculateBasePrice($product);
		$inputPriceType = $this->pricingSetting->getInputPriceType();

		if ($inputPriceType === PricingSetting::INPUT_PRICE_TYPE_WITHOUT_VAT) {
			$inputPrice = $this->inputPriceCalculation->getInputPriceWithoutVat(
				$productPrice->getPriceWithVat(),
				$newVatPercent
			);
		} elseif ($inputPriceType === PricingSetting::INPUT_PRICE_TYPE_WITH_VAT) {
			$inputPrice = $productPrice->getPriceWithVat();
		} else {
			throw new \SS6\ShopBundle\Model\Pricing\Exception\InvalidInputPriceTypeException(
				sprintf('Input price type "%s" is not valid', $inputPriceType)
			);
		}

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
	 * @param \SS6\ShopBundle\Model\Pricing\Vat $vat
	 */
	public function changeVat(Product $product, Vat $vat) {
		$product->changeVat($vat);
		$this->productPriceRecalculationScheduler->scheduleRecalculatePriceForProduct($product);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup[] $pricingGroups
	 * @return \SS6\ShopBundle\Tests\Model\Product\Pricing\ProductSellingPrice[]
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

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param \SS6\ShopBundle\Model\Product\Parameter\ProductParameterValueData|array $productParameterValuesDataItem
	 * @return \SS6\ShopBundle\Model\Product\Parameter\ProductParameterValue[]
	 */
	public function createProductParameterValues($product, $productParameterValuesDataItem) {
		if ($productParameterValuesDataItem instanceof ProductParameterValueData) {
			$productParameterValuesDataItem->setProduct($product);
			$productParameterValue = new ProductParameterValue($productParameterValuesDataItem);
			return array($productParameterValue);
		} else {
			return $this->createProductParameterValuesFromArray($product, $productParameterValuesDataItem);
		}
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param array $productParameterValuesDataItem
	 * @return \SS6\ShopBundle\Model\Product\Parameter\ProductParameterValue
	 */
	private function createProductParameterValuesFromArray(Product $product, array $productParameterValuesDataItem) {
		$productParameterValues = array();
		$productParameterValueData = new ProductParameterValueData();
		$productParameterValueData->setProduct($product);
		$productParameterValueData->setParameter($productParameterValuesDataItem['parameter']);
		foreach ($productParameterValuesDataItem['value'] as $locale => $parameterLocalizedValue) {
			if ($parameterLocalizedValue !== null) {
				$productParameterValueData->setLocale($locale);
				$productParameterValueData->setValue($parameterLocalizedValue);
				$productParameterValue = new ProductParameterValue($productParameterValueData);
				$productParameterValues[] = $productParameterValue;
			}
		}

		return $productParameterValues;
	}

}
