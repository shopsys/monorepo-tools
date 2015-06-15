<?php

namespace SS6\ShopBundle\Model\Product\Pricing;

use SS6\ShopBundle\Model\Pricing\BasePriceCalculation;
use SS6\ShopBundle\Model\Pricing\Currency\CurrencyFacade;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroup;
use SS6\ShopBundle\Model\Pricing\PricingSetting;
use SS6\ShopBundle\Model\Product\Pricing\ProductManualInputPriceRepository;
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
	 * @var \SS6\ShopBundle\Model\Product\Pricing\ProductManualInputPriceRepository
	 */
	private $productManualInputPriceRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Currency\CurrencyFacade
	 */
	private $currencyFacade;

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\BasePriceCalculation $basePriceCalculation
	 * @param \SS6\ShopBundle\Model\Pricing\PricingSetting $pricingSetting
	 * @param \SS6\ShopBundle\Model\Product\Pricing\ProductManualInputPriceRepository $productManualInputPriceRepository
	 */
	public function __construct(
		BasePriceCalculation $basePriceCalculation,
		PricingSetting $pricingSetting,
		ProductManualInputPriceRepository $productManualInputPriceRepository,
		CurrencyFacade $currencyFacade
	) {
		$this->pricingSetting = $pricingSetting;
		$this->basePriceCalculation = $basePriceCalculation;
		$this->productManualInputPriceRepository = $productManualInputPriceRepository;
		$this->currencyFacade = $currencyFacade;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 * @return \SS6\ShopBundle\Model\Pricing\Price
	 */
	public function calculatePrice(Product $product, PricingGroup $pricingGroup) {
		$priceCalculationType = $product->getPriceCalculationType();
		if ($priceCalculationType === Product::PRICE_CALCULATION_TYPE_AUTO) {
			return $this->calculateBasePriceForPricingGroupAuto($product, $pricingGroup);
		} elseif ($priceCalculationType === Product::PRICE_CALCULATION_TYPE_MANUAL) {
			return $this->calculateBasePriceForPricingGroupManual($product, $pricingGroup);
		} else {
			$message = 'Product price calculation type ' . $priceCalculationType . ' is not supported';
			throw new \SS6\ShopBundle\Model\Product\Exception\InvalidPriceCalculationTypeException($message);
		}
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @return \SS6\ShopBundle\Model\Pricing\Price
	 */
	public function calculateBasePrice(Product $product) {
		return $this->basePriceCalculation->calculateBasePrice(
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
	private function calculateBasePriceForPricingGroupManual(Product $product, PricingGroup $pricingGroup) {
		$manualInputPrice = $this->productManualInputPriceRepository->findByProductAndPricingGroup($product, $pricingGroup);
		if ($manualInputPrice !== null) {
			$price = $manualInputPrice->getInputPrice();
		} else {
			$price = 0;
		}
		return $this->basePriceCalculation->calculateBasePrice(
			$price,
			$this->pricingSetting->getInputPriceType(),
			$product->getVat()
		);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 * @return \SS6\ShopBundle\Model\Pricing\Price
	 */
	private function calculateBasePriceForPricingGroupAuto(Product $product, PricingGroup $pricingGroup) {
		$basePrice = $this->calculateBasePrice($product);

		return $this->basePriceCalculation->applyCoefficients(
			$basePrice,
			$product->getVat(),
			[$pricingGroup->getCoefficient(), $this->getDomainDefaultCurrencyReversedExchangeRate($pricingGroup)]
		);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 * @return string
	 */
	private function getDomainDefaultCurrencyReversedExchangeRate(PricingGroup $pricingGroup) {
		$domainId = $pricingGroup->getDomainId();
		$domainDefaultCurrencyId = $this->pricingSetting->getDomainDefaultCurrencyIdByDomainId($domainId);
		$currency = $this->currencyFacade->getById($domainDefaultCurrencyId);

		return $currency->getReversedExchangeRate();
	}

}
