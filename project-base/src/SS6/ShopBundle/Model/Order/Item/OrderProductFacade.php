<?php

namespace SS6\ShopBundle\Model\Order\Item;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Order\Item\OrderProductService;
use SS6\ShopBundle\Model\Product\Availability\ProductAvailabilityRecalculationScheduler;
use SS6\ShopBundle\Model\Product\ProductHiddenRecalculator;
use SS6\ShopBundle\Model\Product\ProductSellingDeniedRecalculator;
use SS6\ShopBundle\Model\Product\ProductVisibilityFacade;

class OrderProductFacade {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductHiddenRecalculator
	 */
	private $productHiddenRecalculator;

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductSellingDeniedRecalculator
	 */
	private $productSellingDeniedRecalculator;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Availability\ProductAvailabilityRecalculationScheduler
	 */
	private $productAvailabilityRecalculationScheduler;

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductVisibilityFacade
	 */
	private $productVisibilityFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Order\Item\OrderProductService
	 */
	private $orderProductService;

	public function __construct(
		EntityManager $em,
		ProductHiddenRecalculator $productHiddenRecalculator,
		ProductSellingDeniedRecalculator $productSellingDeniedRecalculator,
		ProductAvailabilityRecalculationScheduler $productAvailabilityRecalculationScheduler,
		ProductVisibilityFacade $productVisibilityFacade,
		OrderProductService $orderProductService
	) {
		$this->em = $em;
		$this->productHiddenRecalculator = $productHiddenRecalculator;
		$this->productSellingDeniedRecalculator = $productSellingDeniedRecalculator;
		$this->productAvailabilityRecalculationScheduler = $productAvailabilityRecalculationScheduler;
		$this->productVisibilityFacade = $productVisibilityFacade;
		$this->orderProductService = $orderProductService;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Item\OrderProduct[] $orderProducts
	 */
	public function subtractOrderProductsFromStock(array $orderProducts) {
		$this->orderProductService->subtractOrderProductsFromStock($orderProducts);
		$this->em->flush();
		$this->runRecalculationsAfterStockQuantityChange($orderProducts);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Item\OrderProduct[] $orderProducts
	 */
	public function addOrderProductsToStock(array $orderProducts) {
		$this->orderProductService->addOrderProductsToStock($orderProducts);
		$this->em->flush();
		$this->runRecalculationsAfterStockQuantityChange($orderProducts);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Item\OrderProduct[] $orderProducts
	 */
	private function runRecalculationsAfterStockQuantityChange(array $orderProducts) {
		$relevantProducts = $this->orderProductService->getProductsUsingStockFromOrderProducts($orderProducts);
		foreach ($relevantProducts as $relevantProduct) {
			$this->productSellingDeniedRecalculator->calculateSellingDeniedForProduct($relevantProduct);
			$this->productHiddenRecalculator->calculateHiddenForProduct($relevantProduct);
			$this->productAvailabilityRecalculationScheduler->scheduleRecalculateAvailabilityForProduct($relevantProduct);
			$relevantProduct->markForVisibilityRecalculation();
			$this->productVisibilityFacade->refreshProductsVisibilityForMarked();
		}
	}
}
