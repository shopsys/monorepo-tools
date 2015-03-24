<?php

namespace SS6\ShopBundle\Model\Product;

use SS6\ShopBundle\Model\Pricing\Currency\CurrencyFacade;
use SS6\ShopBundle\Model\Pricing\InputPriceCalculation;
use SS6\ShopBundle\Model\Pricing\PricingSetting;
use SS6\ShopBundle\Model\Pricing\Vat\VatFacade;
use SS6\ShopBundle\Model\Product\Pricing\ProductManualInputPriceFacade;
use SS6\ShopBundle\Model\Product\Pricing\ProductPriceCalculation;

class ProductDataFactory {

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Vat\VatFacade
	 */
	private $vatFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Pricing\ProductManualInputPriceFacade
	 */
	private $productManualInputPriceFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Currency\CurrencyFacade
	 */
	private $currencyFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Pricing\ProductPriceCalculation
	 */
	private $productPriceCalculation;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\PricingSetting
	 */
	private $pricingSetting;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\InputPriceCalculation
	 */
	private $inputPriceCalculation;

	public function __construct(
		VatFacade $vatFacade,
		ProductManualInputPriceFacade $productManualInputPriceFacade,
		CurrencyFacade $currencyFacade,
		ProductPriceCalculation $productPriceCalculation,
		PricingSetting $pricingSetting,
		InputPriceCalculation $inputPriceCalculation
	) {
		$this->vatFacade = $vatFacade;
		$this->productManualInputPriceFacade = $productManualInputPriceFacade;
		$this->currencyFacade = $currencyFacade;
		$this->productPriceCalculation = $productPriceCalculation;
		$this->pricingSetting = $pricingSetting;
		$this->inputPriceCalculation = $inputPriceCalculation;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\ProductData
	 */
	public function createDefault() {
		$productData = new ProductData();

		$productData->vat = $this->vatFacade->getDefaultVat();

		return $productData;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param \SS6\ShopBundle\Model\Product\ProductDomain[] $productDomains
	 * @return \SS6\ShopBundle\Model\Product\ProductData
	 */
	public function createFromProduct(Product $product, array $productDomains) {
		$productData = $this->createDefault();

		$translations = $product->getTranslations();
		$names = [];
		$descriptions = [];
		foreach ($translations as $translation) {
			$names[$translation->getLocale()] = $translation->getName();
			$descriptions[$translation->getLocale()] = $translation->getDescription();
		}
		$productData->name = $names;
		$productData->description = $descriptions;

		$productData->catnum = $product->getCatnum();
		$productData->partno = $product->getPartno();
		$productData->ean = $product->getEan();
		$productData->price = $this->getInputPrice($product);
		$productData->vat = $product->getVat();
		$productData->sellingFrom = $product->getSellingFrom();
		$productData->sellingTo = $product->getSellingTo();
		$productData->flags = $product->getFlags()->toArray();
		$productData->usingStock = $product->isUsingStock();
		$productData->stockQuantity = $product->getStockQuantity();
		$productData->availability = $product->getAvailability();
		$productData->outOfStockAvailability = $product->getOutOfStockAvailability();

		$productData->hidden = $product->isHidden();
		$hiddenOnDomains = [];
		foreach ($productDomains as $productDomain) {
			if ($productDomain->isHidden()) {
				$hiddenOnDomains[] = $productDomain->getDomainId();
			}
		}
		$productData->hiddenOnDomains = $hiddenOnDomains;

		$productData->categories = $product->getCategories()->toArray();
		$productData->priceCalculationType = $product->getPriceCalculationType();
		$productData->accessories = $product->getAccessories();

		return $productData;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @return string|null
	 */
	private function getInputPrice(Product $product) {
		if ($product->getPriceCalculationType() === Product::PRICE_CALCULATION_TYPE_AUTO) {
			return $product->getPrice();
		} elseif ($product->getPriceCalculationType() === Product::PRICE_CALCULATION_TYPE_MANUAL) {
			$inputPriceType = $this->pricingSetting->getInputPriceType();

			$maxSellingPriceWithVatInDefaultCurrency = $this->getMaxSellingPriceWithVatInDefaultCurrency($product);

			if ($maxSellingPriceWithVatInDefaultCurrency === null) {
				return null;
			}

			return $this->inputPriceCalculation->getInputPrice(
				$inputPriceType,
				$maxSellingPriceWithVatInDefaultCurrency,
				$product->getVat()->getPercent()
			);
		}
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @return string|null
	 */
	private function getMaxSellingPriceWithVatInDefaultCurrency(Product $product) {
		$defaultCurrency = $this->currencyFacade->getDefaultCurrency();
		$manualInputPrices = $this->productManualInputPriceFacade->getAllByProduct($product);

		$maxSellingPriceWithVatInDefaultCurrency = null;
		foreach ($manualInputPrices as $manualInputPrice) {
			$manualPriceDomainId = $manualInputPrice->getPricingGroup()->getDomainId();
			$manualPriceCurrency = $this->currencyFacade->getDomainDefaultCurrencyByDomainId($manualPriceDomainId);

			if ($manualPriceCurrency === $defaultCurrency) {
				$productPrice = $this->productPriceCalculation->calculatePrice($product, $manualInputPrice->getPricingGroup());

				if ($maxSellingPriceWithVatInDefaultCurrency === null
					|| $productPrice->getPriceWithVat() > $maxSellingPriceWithVatInDefaultCurrency
				) {
					$maxSellingPriceWithVatInDefaultCurrency = $productPrice->getPriceWithVat();
				}
			}
		}

		return $maxSellingPriceWithVatInDefaultCurrency;
	}

}
