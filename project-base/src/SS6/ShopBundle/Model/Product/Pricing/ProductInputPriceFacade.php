<?php

namespace SS6\ShopBundle\Model\Product\Pricing;

use SS6\ShopBundle\Model\Domain\DomainFacade;
use SS6\ShopBundle\Model\Pricing\Currency\CurrencyFacade;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroupFacade;
use SS6\ShopBundle\Model\Pricing\PricingSetting;
use SS6\ShopBundle\Model\Product\Pricing\ProductInputPriceService;
use SS6\ShopBundle\Model\Product\Pricing\ProductManualInputPriceRepository;
use SS6\ShopBundle\Model\Product\Product;

class ProductInputPriceFacade {

	/**
	 * @var \SS6\ShopBundle\Model\Product\Pricing\ProductInputPriceService
	 */
	private $productInputPriceService;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Currency\CurrencyFacade
	 */
	private $currencyFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\PricingSetting
	 */
	private $pricingSetting;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Pricing\ProductManualInputPriceRepository
	 */
	private $productManualInputPriceRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Domain\DomainFacade
	 */
	private $domainFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Group\PricingGroupFacade
	 */
	private $pricingGroupFacade;

	public function __construct(
		ProductInputPriceService $productInputPriceService,
		CurrencyFacade $currencyFacade,
		PricingSetting $pricingSetting,
		ProductManualInputPriceRepository $productManualInputPriceRepository,
		PricingGroupFacade $pricingGroupFacade,
		DomainFacade $domainFacade
	) {
		$this->productInputPriceService = $productInputPriceService;
		$this->currencyFacade = $currencyFacade;
		$this->pricingSetting = $pricingSetting;
		$this->productManualInputPriceRepository = $productManualInputPriceRepository;
		$this->domainFacade = $domainFacade;
		$this->pricingGroupFacade = $pricingGroupFacade;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @return string|null
	 */
	public function getInputPrice(Product $product) {
		$inputPriceType = $this->pricingSetting->getInputPriceType();
		$defaultCurrency = $this->currencyFacade->getDefaultCurrency();
		$manualInputPricesInDefaultCurrency = $this->productManualInputPriceRepository->getByProductAndDomainConfigs(
			$product,
			$this->domainFacade->getDomainConfigsByCurrency($defaultCurrency)
		);

		return $this->productInputPriceService->getInputPrice($product, $inputPriceType, $manualInputPricesInDefaultCurrency);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @return string[pricingGroupId]
	 */
	public function getManualInputPricesData(Product $product) {
		$pricingGroups = $this->pricingGroupFacade->getAll();
		$inputPriceType = $this->pricingSetting->getInputPriceType();
		$manualInputPrices = $this->productManualInputPriceRepository->getByProduct($product);

		return $this->productInputPriceService->getManualInputPricesData(
			$product,
			$inputPriceType,
			$pricingGroups,
			$manualInputPrices
		);
	}

}
