<?php

namespace SS6\ShopBundle\Model\Product\Pricing;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Product\Pricing\ProductCalculatedPriceRepository;
use SS6\ShopBundle\Model\Product\Pricing\ProductPriceCalculation;
use SS6\ShopBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler;
use SS6\ShopBundle\Model\Product\ProductRepository;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroupFacade;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class ProductPriceRecalculator {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductRepository
	 */
	private $productRepository;

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

	public function __construct(
		EntityManager $em,
		ProductRepository $productRepository,
		ProductPriceCalculation $productPriceCalculation,
		ProductCalculatedPriceRepository $productCalculatedPriceRepository,
		ProductPriceRecalculationScheduler $productPriceRecalculationScheduler,
		PricingGroupFacade $pricingGroupFacade
	) {
		$this->em = $em;
		$this->productRepository = $productRepository;
		$this->productPriceCalculation = $productPriceCalculation;
		$this->productCalculatedPriceRepository = $productCalculatedPriceRepository;
		$this->productPriceRecalculationScheduler = $productPriceRecalculationScheduler;
		$this->pricingGroupFacade = $pricingGroupFacade;
	}

	public function runScheduledRecalculations() {
		$products = $this->productPriceRecalculationScheduler->getProductsScheduledForRecalculation();
		$this->recalculatePricesForProducts($products);
		$this->productPriceRecalculationScheduler->cleanSchedule();
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product[] $products
	 */
	private function recalculatePricesForProducts(array $products) {
		$allPricingGroups = $this->pricingGroupFacade->getAll();

		foreach ($products as $product) {
			foreach ($allPricingGroups as $pricingGroup) {
				$price = $this->productPriceCalculation->calculatePrice($product, $pricingGroup);
				$this->productCalculatedPriceRepository->saveCalculatedPrice($product, $pricingGroup, $price->getPriceWithVat());
			}
		}
		$this->em->flush();
	}

	/**
	 * @param \Symfony\Component\HttpKernel\Event\FilterResponseEvent $event
	 */
	public function onKernelResponse(FilterResponseEvent $event) {
		$this->runScheduledRecalculations();
	}

}
