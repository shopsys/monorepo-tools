<?php

namespace SS6\ShopBundle\Model\Product\Pricing;

use SS6\ShopBundle\Model\Pricing\BasePriceCalculation;
use SS6\ShopBundle\Model\Pricing\Currency\CurrencyFacade;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroup;
use SS6\ShopBundle\Model\Pricing\PricingService;
use SS6\ShopBundle\Model\Pricing\PricingSetting;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;
use SS6\ShopBundle\Model\Product\Pricing\ProductManualInputPriceRepository;
use SS6\ShopBundle\Model\Product\Pricing\ProductPrice;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductRepository;

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
	 * @var \SS6\ShopBundle\Model\Product\ProductRepository
	 */
	private $productRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\PricingService
	 */
	private $pricingService;

	public function __construct(
		BasePriceCalculation $basePriceCalculation,
		PricingSetting $pricingSetting,
		ProductManualInputPriceRepository $productManualInputPriceRepository,
		CurrencyFacade $currencyFacade,
		ProductRepository $productRepository,
		PricingService $pricingService
	) {
		$this->pricingSetting = $pricingSetting;
		$this->basePriceCalculation = $basePriceCalculation;
		$this->productManualInputPriceRepository = $productManualInputPriceRepository;
		$this->currencyFacade = $currencyFacade;
		$this->productRepository = $productRepository;
		$this->pricingService = $pricingService;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param int $domainId
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 * @return \SS6\ShopBundle\Model\Product\Pricing\ProductPrice
	 */
	public function calculatePrice(Product $product, $domainId, PricingGroup $pricingGroup) {
		if ($product->isMainVariant()) {
			return $this->calculateMainVariantPrice($product, $domainId, $pricingGroup);
		}

		$priceCalculationType = $product->getPriceCalculationType();
		if ($priceCalculationType === Product::PRICE_CALCULATION_TYPE_AUTO) {
			return $this->calculateProductPriceForPricingGroupAuto($product, $pricingGroup, $domainId);
		} elseif ($priceCalculationType === Product::PRICE_CALCULATION_TYPE_MANUAL) {
			return $this->calculateProductPriceForPricingGroupManual($product, $pricingGroup);
		} else {
			throw new \SS6\ShopBundle\Model\Product\Exception\InvalidPriceCalculationTypeException(
				$priceCalculationType
			);
		}
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $mainVariant
	 * @param int $domainId
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 * @return \SS6\ShopBundle\Model\Product\Pricing\ProductPrice
	 */
	private function calculateMainVariantPrice(Product $mainVariant, $domainId, PricingGroup $pricingGroup) {
		$variants = $this->productRepository->getAllSellableVariantsByMainVariant(
			$mainVariant,
			$domainId,
			$pricingGroup
		);
		if (count($variants) === 0) {
			$message = 'Main variant ID = ' . $mainVariant->getId() . ' has no sellable variants.';
			throw new \SS6\ShopBundle\Model\Product\Pricing\Exception\MainVariantPriceCalculationException($message);
		}

		$variantPrices = [];
		foreach ($variants as $variant) {
			$variantPrices[] = $this->calculatePrice($variant, $domainId, $pricingGroup);
		}

		$minVariantPrice = $this->pricingService->getMinimumPriceByPriceWithoutVat($variantPrices);
		$from = $this->pricingService->arePricesDifferent($variantPrices);

		return new ProductPrice($minVariantPrice, $from);
	}

	/**
	 * @param string $inputPrice
	 * @param \SS6\ShopBundle\Model\Pricing\Vat\Vat $vat
	 * @return \SS6\ShopBundle\Model\Pricing\Price
	 */
	private function calculateBasePrice($inputPrice, Vat $vat) {
		return $this->basePriceCalculation->calculateBasePrice(
			$inputPrice,
			$this->pricingSetting->getInputPriceType(),
			$vat
		);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 * @return \SS6\ShopBundle\Model\Product\Pricing\ProductPrice
	 */
	private function calculateProductPriceForPricingGroupManual(Product $product, PricingGroup $pricingGroup) {
		$manualInputPrice = $this->productManualInputPriceRepository->findByProductAndPricingGroup($product, $pricingGroup);
		if ($manualInputPrice !== null) {
			$inputPrice = $manualInputPrice->getInputPrice();
		} else {
			$inputPrice = 0;
		}
		$basePrice = $this->calculateBasePrice($inputPrice, $product->getVat());

		return new ProductPrice($basePrice, false);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 * @param int $domainId
	 * @return \SS6\ShopBundle\Model\Product\Pricing\ProductPrice
	 */
	private function calculateProductPriceForPricingGroupAuto(Product $product, PricingGroup $pricingGroup, $domainId) {
		$basePrice = $this->calculateBasePrice($product->getPrice(), $product->getVat());

		$price = $this->basePriceCalculation->applyCoefficients(
			$basePrice,
			$product->getVat(),
			[$pricingGroup->getCoefficient(), $this->getDomainDefaultCurrencyReversedExchangeRate($domainId)]
		);

		return new ProductPrice($price, false);
	}

	/**
	 * @param int $domainId
	 * @return string
	 */
	private function getDomainDefaultCurrencyReversedExchangeRate($domainId) {
		$domainDefaultCurrencyId = $this->pricingSetting->getDomainDefaultCurrencyIdByDomainId($domainId);
		$currency = $this->currencyFacade->getById($domainDefaultCurrencyId);

		return $currency->getReversedExchangeRate();
	}

}
