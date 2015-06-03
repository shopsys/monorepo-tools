<?php

namespace SS6\ShopBundle\Model\Product\Pricing;

use Closure;
use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Domain\DomainFacade;
use SS6\ShopBundle\Model\Pricing\Currency\CurrencyFacade;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroupFacade;
use SS6\ShopBundle\Model\Pricing\PricingSetting;
use SS6\ShopBundle\Model\Product\Pricing\ProductInputPriceService;
use SS6\ShopBundle\Model\Product\Pricing\ProductManualInputPriceRepository;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductRepository;
use SS6\ShopBundle\Model\Product\ProductService;

class ProductInputPriceFacade {

	const BATCH_SIZE = 50;

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

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

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductRepository
	 */
	private $productRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductService
	 */
	private $productService;

	public function __construct(
		EntityManager $em,
		ProductInputPriceService $productInputPriceService,
		CurrencyFacade $currencyFacade,
		PricingSetting $pricingSetting,
		ProductManualInputPriceRepository $productManualInputPriceRepository,
		PricingGroupFacade $pricingGroupFacade,
		DomainFacade $domainFacade,
		ProductRepository $productRepository,
		ProductService $productService
	) {
		$this->em = $em;
		$this->productInputPriceService = $productInputPriceService;
		$this->currencyFacade = $currencyFacade;
		$this->pricingSetting = $pricingSetting;
		$this->productManualInputPriceRepository = $productManualInputPriceRepository;
		$this->domainFacade = $domainFacade;
		$this->pricingGroupFacade = $pricingGroupFacade;
		$this->productRepository = $productRepository;
		$this->productService = $productService;
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

	/**
	 * @param \Closure $canRunClosure
	 * @return int
	 */
	public function replaceVatAndRecalculateInputPrices(Closure $canRunClosure) {
		$productIterator = $this->productRepository->getProductIteratorForReplaceVat();
		$count = 0;

		foreach ($productIterator as $row) {
			$product = $row[0];
			/* @var $product \SS6\ShopBundle\Model\Product\Product */
			if (!$canRunClosure()) {
				return $count;
			}

			$newVat = $product->getVat()->getReplaceWith();
			$this->productService->recalculateInputPriceForNewVatPercent($product, $newVat->getPercent());
			$product->changeVat($newVat);

			$count++;
			if ($count % self::BATCH_SIZE === 0) {
				$this->em->flush();
				$this->em->clear();
			}
		}
		$this->em->flush();
		$this->em->clear();

		return $count;
	}

}
