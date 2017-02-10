<?php

namespace Shopsys\ShopBundle\Model\Order\Item;

use Doctrine\ORM\EntityManager;
use Shopsys\ShopBundle\Model\Module\ModuleFacade;
use Shopsys\ShopBundle\Model\Module\ModuleList;
use Shopsys\ShopBundle\Model\Order\Item\OrderProductService;
use Shopsys\ShopBundle\Model\Product\Availability\ProductAvailabilityRecalculationScheduler;
use Shopsys\ShopBundle\Model\Product\ProductHiddenRecalculator;
use Shopsys\ShopBundle\Model\Product\ProductSellingDeniedRecalculator;
use Shopsys\ShopBundle\Model\Product\ProductService;
use Shopsys\ShopBundle\Model\Product\ProductVisibilityFacade;

class OrderProductFacade {

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\ProductHiddenRecalculator
     */
    private $productHiddenRecalculator;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\ProductSellingDeniedRecalculator
     */
    private $productSellingDeniedRecalculator;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Availability\ProductAvailabilityRecalculationScheduler
     */
    private $productAvailabilityRecalculationScheduler;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\ProductVisibilityFacade
     */
    private $productVisibilityFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Order\Item\OrderProductService
     */
    private $orderProductService;

    /**
     * @var \Shopsys\ShopBundle\Model\Module\ModuleFacade
     */
    private $moduleFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\ProductService
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
     * @param \Shopsys\ShopBundle\Model\Order\Item\OrderProduct[] $orderProducts
     */
    public function subtractOrderProductsFromStock(array $orderProducts) {
        if ($this->moduleFacade->isEnabled(ModuleList::PRODUCT_STOCK_CALCULATIONS)) {
            $this->orderProductService->subtractOrderProductsFromStock($orderProducts);
            $this->em->flush();
            $this->runRecalculationsAfterStockQuantityChange($orderProducts);
        }
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Order\Item\OrderProduct[] $orderProducts
     */
    public function addOrderProductsToStock(array $orderProducts) {
        if ($this->moduleFacade->isEnabled(ModuleList::PRODUCT_STOCK_CALCULATIONS)) {
            $this->orderProductService->returnOrderProductsToStock($orderProducts);
            $this->em->flush();
            $this->runRecalculationsAfterStockQuantityChange($orderProducts);
        }
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Order\Item\OrderProduct[] $orderProducts
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
