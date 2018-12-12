<?php

namespace Shopsys\FrameworkBundle\Model\Order\Item;

use Doctrine\ORM\EntityManagerInterface;
use Shopsys\FrameworkBundle\Model\Module\ModuleFacade;
use Shopsys\FrameworkBundle\Model\Module\ModuleList;
use Shopsys\FrameworkBundle\Model\Product\Availability\ProductAvailabilityRecalculationScheduler;
use Shopsys\FrameworkBundle\Model\Product\ProductHiddenRecalculator;
use Shopsys\FrameworkBundle\Model\Product\ProductSellingDeniedRecalculator;
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
     * @var \Shopsys\FrameworkBundle\Model\Module\ModuleFacade
     */
    protected $moduleFacade;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductHiddenRecalculator $productHiddenRecalculator
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductSellingDeniedRecalculator $productSellingDeniedRecalculator
     * @param \Shopsys\FrameworkBundle\Model\Product\Availability\ProductAvailabilityRecalculationScheduler $productAvailabilityRecalculationScheduler
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductVisibilityFacade $productVisibilityFacade
     * @param \Shopsys\FrameworkBundle\Model\Module\ModuleFacade $moduleFacade
     */
    public function __construct(
        EntityManagerInterface $em,
        ProductHiddenRecalculator $productHiddenRecalculator,
        ProductSellingDeniedRecalculator $productSellingDeniedRecalculator,
        ProductAvailabilityRecalculationScheduler $productAvailabilityRecalculationScheduler,
        ProductVisibilityFacade $productVisibilityFacade,
        ModuleFacade $moduleFacade
    ) {
        $this->em = $em;
        $this->productHiddenRecalculator = $productHiddenRecalculator;
        $this->productSellingDeniedRecalculator = $productSellingDeniedRecalculator;
        $this->productAvailabilityRecalculationScheduler = $productAvailabilityRecalculationScheduler;
        $this->productVisibilityFacade = $productVisibilityFacade;
        $this->moduleFacade = $moduleFacade;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\OrderProduct[] $orderProducts
     */
    public function subtractOrderProductsFromStock(array $orderProducts)
    {
        if ($this->moduleFacade->isEnabled(ModuleList::PRODUCT_STOCK_CALCULATIONS)) {
            $orderProductsUsingStock = $this->getOrderProductsUsingStockFromOrderProducts($orderProducts);
            foreach ($orderProductsUsingStock as $orderProductUsingStock) {
                $product = $orderProductUsingStock->getProduct();
                $product->subtractStockQuantity($orderProductUsingStock->getQuantity());
            }
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
            $orderProductsUsingStock = $this->getOrderProductsUsingStockFromOrderProducts($orderProducts);
            foreach ($orderProductsUsingStock as $orderProductUsingStock) {
                $product = $orderProductUsingStock->getProduct();
                $product->addStockQuantity($orderProductUsingStock->getQuantity());
            }
            $this->em->flush();
            $this->runRecalculationsAfterStockQuantityChange($orderProducts);
        }
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\OrderProduct[] $orderProducts
     */
    protected function runRecalculationsAfterStockQuantityChange(array $orderProducts)
    {
        $orderProductsUsingStock = $this->getOrderProductsUsingStockFromOrderProducts($orderProducts);
        $relevantProducts = [];
        foreach ($orderProductsUsingStock as $orderProductUsingStock) {
            $relevantProducts[] = $orderProductUsingStock->getProduct();
        }

        foreach ($relevantProducts as $relevantProduct) {
            $this->productSellingDeniedRecalculator->calculateSellingDeniedForProduct($relevantProduct);
            $this->productHiddenRecalculator->calculateHiddenForProduct($relevantProduct);
            $this->productAvailabilityRecalculationScheduler->scheduleProductForImmediateRecalculation($relevantProduct);
            $relevantProduct->markForVisibilityRecalculation();
        }
        $this->em->flush($relevantProducts);

        $this->productVisibilityFacade->refreshProductsVisibilityForMarked();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\OrderProduct[] $orderProducts
     * @return \Shopsys\FrameworkBundle\Model\Order\Item\OrderProduct[]
     */
    protected function getOrderProductsUsingStockFromOrderProducts(array $orderProducts)
    {
        $orderProductsUsingStock = [];
        foreach ($orderProducts as $orderProduct) {
            $product = $orderProduct->getProduct();
            if ($product !== null && $product->isUsingStock()) {
                $orderProductsUsingStock[] = $orderProduct;
            }
        }

        return $orderProductsUsingStock;
    }
}
