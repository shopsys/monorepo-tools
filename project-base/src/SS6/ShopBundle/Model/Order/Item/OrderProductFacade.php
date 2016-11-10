<?php

namespace SS6\ShopBundle\Model\Order\Item;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Module\ModuleFacade;
use SS6\ShopBundle\Model\Module\ModuleList;
use SS6\ShopBundle\Model\Order\Item\OrderProductService;
use SS6\ShopBundle\Model\Product\Availability\ProductAvailabilityRecalculationScheduler;
use SS6\ShopBundle\Model\Product\ProductHiddenRecalculator;
use SS6\ShopBundle\Model\Product\ProductSellingDeniedRecalculator;
use SS6\ShopBundle\Model\Product\ProductService;
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

	/**
	 * @var \SS6\ShopBundle\Model\Module\ModuleFacade
	 */
	private $moduleFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductService
	 */
	private $productService;

	public function __construct(
		EntityManager $em,
		ProductHiddenRecalculator $productHiddenRecalculator,
		ProductSellingDeniedRecalculator $productSellingDeniedRecalculator,
		ProductAvailabilityRecalculationScheduler $productAvailabilityRecalculationScheduler,
		ProductVisibilityFacade $productVisibilityFacade,
		OrderProductService $orderProductService,
		ModuleFacade $moduleFacade,
		ProductService $productService
	) {
		$this->em = $em;
		$this->productHiddenRecalculator = $productHiddenRecalculator;
		$this->productSellingDeniedRecalculator = $productSellingDeniedRecalculator;
		$this->productAvailabilityRecalculationScheduler = $productAvailabilityRecalculationScheduler;
		$this->productVisibilityFacade = $productVisibilityFacade;
		$this->orderProductService = $orderProductService;
		$this->moduleFacade = $moduleFacade;
		$this->productService = $productService;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Item\OrderProduct[] $orderProducts
	 */
	public function subtractOrderProductsFromStock(array $orderProducts) {
		if ($this->moduleFacade->isEnabled(ModuleList::PRODUCT_STOCK_CALCULATIONS)) {
			$this->orderProductService->subtractOrderProductsFromStock($orderProducts);
			$this->em->flush();
			$this->runRecalculationsAfterStockQuantityChange($orderProducts);
		}
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Item\OrderProduct[] $orderProducts
	 */
	public function addOrderProductsToStock(array $orderProducts) {
		if ($this->moduleFacade->isEnabled(ModuleList::PRODUCT_STOCK_CALCULATIONS)) {
			$this->orderProductService->returnOrderProductsToStock($orderProducts);
			$this->em->flush();
			$this->runRecalculationsAfterStockQuantityChange($orderProducts);
		}
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Item\OrderProduct[] $orderProducts
	 */
	private function runRecalculationsAfterStockQuantityChange(array $orderProducts) {
		$relevantProducts = $this->orderProductService->getProductsUsingStockFromOrderProducts($orderProducts);
		foreach ($relevantProducts as $relevantProduct) {
			$this->productSellingDeniedRecalculator->calculateSellingDeniedForProduct($relevantProduct);
			$this->productHiddenRecalculator->calculateHiddenForProduct($relevantProduct);
			$this->productAvailabilityRecalculationScheduler->scheduleProductForImmediateRecalculation($relevantProduct);
			$this->productService->markProductForVisibilityRecalculation($relevantProduct);
		}
		$this->em->flush($relevantProducts);

		$this->productVisibilityFacade->refreshProductsVisibilityForMarked();
	}
}
