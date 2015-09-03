<?php

namespace SS6\ShopBundle\Model\Product\Pricing;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroupFacade;
use SS6\ShopBundle\Model\Product\Pricing\ProductCalculatedPriceRepository;
use SS6\ShopBundle\Model\Product\Pricing\ProductPriceCalculation;
use SS6\ShopBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler;
use SS6\ShopBundle\Model\Product\Product;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class ProductPriceRecalculator {

	const BATCH_SIZE = 100;

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Pricing\ProductPriceCalculation
	 */
	private $productPriceCalculation;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Pricing\ProductCalculatedPriceRepository
	 */
	private $productCalculatedPriceRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler
	 */
	private $productPriceRecalculationScheduler;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Group\PricingGroupFacade
	 */
	private $pricingGroupFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Group\PricingGroup[]|null
	 */
	private $allPricingGroups;

	public function __construct(
		EntityManager $em,
		ProductPriceCalculation $productPriceCalculation,
		ProductCalculatedPriceRepository $productCalculatedPriceRepository,
		ProductPriceRecalculationScheduler $productPriceRecalculationScheduler,
		PricingGroupFacade $pricingGroupFacade
	) {
		$this->em = $em;
		$this->productPriceCalculation = $productPriceCalculation;
		$this->productCalculatedPriceRepository = $productCalculatedPriceRepository;
		$this->productPriceRecalculationScheduler = $productPriceRecalculationScheduler;
		$this->pricingGroupFacade = $pricingGroupFacade;
	}

	/**
	 * @param callable $canRunCallback
	 * @return int
	 */
	public function runScheduledRecalculationsWhile(callable $canRunCallback) {
		$productRows = $this->productPriceRecalculationScheduler->getProductsIteratorForRecalculation();
		$count = 0;

		foreach ($productRows as $row) {
			if (!$canRunCallback()) {
				return $count;
			}
			$this->recalculateProductPrices($row[0]);
			$count++;
			if ($count % self::BATCH_SIZE === 0) {
				$this->clearCache();
				$this->em->clear();
			}
		}
		$this->clearCache();
		$this->em->clear();

		return $count;
	}

	/**
	 * @return int
	 */
	public function runAllScheduledRecalculations() {
		$this->runScheduledRecalculationsWhile(function () {
			return true;
		});
	}

	public function runImmediateRecalculations() {
		$products = $this->productPriceRecalculationScheduler->getProductsForImmediatelyRecalculation();
		foreach ($products as $product) {
			$this->recalculateProductPrices($product);
		}
		$this->productPriceRecalculationScheduler->cleanImmediatelyRecalculationSchedule();
		$this->clearCache();
	}

	private function clearCache() {
		$this->allPricingGroups = null;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Pricing\Group\PricingGroup[]
	 */
	private function getAllPricingGroups() {
		if ($this->allPricingGroups === null) {
			$this->allPricingGroups = $this->pricingGroupFacade->getAll();
		}

		return $this->allPricingGroups;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 */
	private function recalculateProductPrices(Product $product) {
		foreach ($this->getAllPricingGroups() as $pricingGroup) {
			try {
				$price = $this->productPriceCalculation->calculatePrice($product, $pricingGroup->getDomainId(), $pricingGroup);
				$priceWithVat = $price->getPriceWithVat();
			} catch (\SS6\ShopBundle\Model\Product\Pricing\Exception\MainVariantPriceCalculationException $e) {
				$priceWithVat = 0;
			}
			$this->productCalculatedPriceRepository->saveCalculatedPrice($product, $pricingGroup, $priceWithVat);
		}
		$product->markPriceAsRecalculated();
		$product->markForVisibilityRecalculation();
		$this->em->flush($product);
	}

	/**
	 * @param \Symfony\Component\HttpKernel\Event\FilterResponseEvent $event
	 */
	public function onKernelResponse(FilterResponseEvent $event) {
		if ($event->isMasterRequest()) {
			$this->runImmediateRecalculations();
		}
	}

}
