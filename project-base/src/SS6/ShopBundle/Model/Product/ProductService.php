<?php

namespace SS6\ShopBundle\Model\Product;

use SS6\ShopBundle\Model\Pricing\InputPriceCalculation;
use SS6\ShopBundle\Model\Pricing\PricingSetting;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;

class ProductService {

	/**
	 * @var \SS6\ShopBundle\Model\Product\PriceCalculation
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

	public function __construct(
		PriceCalculation $productPriceCalculation,
		InputPriceCalculation $inputPriceCalculation,
		PricingSetting $pricingSetting
	) {
		$this->productPriceCalculation = $productPriceCalculation;
		$this->inputPriceCalculation = $inputPriceCalculation;
		$this->pricingSetting = $pricingSetting;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product[] $products
	 * @param \SS6\ShopBundle\Model\Pricing\Vat\Vat $newVat
	 */
	public function replaceOldVatWithNewVat(array $products, Vat $newVat) {
		foreach ($products as $product) {
			$this->recalculateInputPriceForNewVatPercent($product, $newVat->getPercent());
			$product->changeVat($newVat);
		}
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product[] $products
	 * @param string $newVatPercent
	 */
	public function recalculateInputPricesForNewVatPercent(array $products, $newVatPercent) {
		foreach ($products as $product) {
			$this->recalculateInputPriceForNewVatPercent($product, $newVatPercent);
		}
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param string $newVatPercent
	 * @throws \SS6\ShopBundle\Model\Pricing\Exception\InvalidInputPriceTypeException
	 */
	public function recalculateInputPriceForNewVatPercent(Product $product, $newVatPercent) {
		$productPrice = $this->productPriceCalculation->calculatePrice($product);
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

		$product->setPrice($inputPrice);
	}

}
