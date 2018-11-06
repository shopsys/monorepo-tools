<?php

namespace Shopsys\FrameworkBundle\Model\Order\Item;

use Doctrine\ORM\EntityManagerInterface;
use Shopsys\FrameworkBundle\Model\Module\ModuleFacade;
use Shopsys\FrameworkBundle\Model\Module\ModuleList;
use Shopsys\FrameworkBundle\Model\Product\Availability\ProductAvailabilityRecalculationScheduler;
use Shopsys\FrameworkBundle\Model\Product\ProductHiddenRecalculator;
use Shopsys\FrameworkBundle\Model\Product\ProductSellingDeniedRecalculator;
use Shopsys\FrameworkBundle\Model\Product\ProductService;
use Shopsys\FrameworkBundle\Model\Product\ProductVisibilityFacade;

class OrderProductFacade
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $em;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductHiddenRecalculator
     */
    protected $productHiddenRecalculator;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductSellingDeniedRecalculator
     */
    protected $productSellingDeniedRecalculator;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Availability\ProductAvailabilityRecalculationScheduler
     */
    protected $productAvailabilityRecalculationScheduler;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductVisibilityFacade
     */
    protected $productVisibilityFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\Item\OrderProductService
     */
    protected $orderProductService;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Module\ModuleFacade
     */
    protected $moduleFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductService
     */
    protected $productService;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductHiddenRecalculator $productHiddenRecalculator
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductSellingDeniedRecalculator $productSellingDeniedRecalculator
     * @param \Shopsys\FrameworkBundle\Model\Product\Availability\ProductAvailabilityRecalculationScheduler $productAvailabilityRecalculationScheduler
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductVisibilityFacade $productVisibilityFacade
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\OrderProductService $orderProductService
     * @param \Shopsys\FrameworkBundle\Model\Module\ModuleFacade $moduleFacade
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductService $productService
     */
    public function __construct(
        EntityManagerInterface $em,
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
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\OrderProduct[] $orderProducts
     */
    public function subtractOrderProductsFromStock(array $orderProducts)
    {
        if ($this->moduleFacade->isEnabled(ModuleList::PRODUCT_STOCK_CALCULATIONS)) {
            $this->orderProductService->subtractOrderProductsFromStock($orderProducts);
            $this->em->flush();
            $this->runRecalculationsAfterStockQuantityChange($orderProducts);
        }
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\OrderProduct[] $orderProducts
     */
    public function addOrderProductsToStock(array $orderProducts)
    {
        if ($this->moduleFacade->isEnabled(ModuleList::PRODUCT_STOCK_CALCULATIONS)) {
            $this->orderProductService->returnOrderProductsToStock($orderProducts);
            $this->em->flush();
            $this->runRecalculationsAfterStockQuantityChange($orderProducts);
        }
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\OrderProduct[] $orderProducts
     */
    protected function runRecalculationsAfterStockQuantityChange(array $orderProducts)
    {
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
