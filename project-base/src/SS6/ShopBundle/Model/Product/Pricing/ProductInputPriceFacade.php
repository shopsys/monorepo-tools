<?php

namespace SS6\ShopBundle\Model\Product\Pricing;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Component\Doctrine\EntityManagerFacade;
use SS6\ShopBundle\Component\Domain\DomainFacade;
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
	 * @var \SS6\ShopBundle\Component\Doctrine\EntityManagerFacade
	 */
	private $entityManagerFacade;

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
	 * @var \SS6\ShopBundle\Component\Domain\DomainFacade
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

	/**
	 * @var \Doctrine\ORM\Internal\Hydration\IterableResult|\SS6\ShopBundle\Model\Product\Product[][0]|null
	 */
	private $productRowsIterator;

	public function __construct(
		EntityManager $em,
		EntityManagerFacade $entityManagerFacade,
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
		$this->entityManagerFacade = $entityManagerFacade;
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
	 * @return bool
	 */
	public function replaceBatchVatAndRecalculateInputPrices() {
		if ($this->productRowsIterator === null) {
			$this->productRowsIterator = $this->productRepository->getProductIteratorForReplaceVat();
		}

		for ($count = 0; $count < self::BATCH_SIZE; $count++) {
			$row = $this->productRowsIterator->next();
			if ($row === false) {
				$this->em->flush();
				$this->entityManagerFacade->clear();

				return false;
			}
			$product = $row[0];
			$newVat = $product->getVat()->getReplaceWith();
			$productManualInputPrices = $this->productManualInputPriceRepository->getByProduct($product);
			$this->productService->recalculateInputPriceForNewVatPercent($product, $productManualInputPrices, $newVat->getPercent());
			$product->changeVat($newVat);
		}

		$this->em->flush();
		$this->entityManagerFacade->clear();

		return true;
	}

}
