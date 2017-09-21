<?php

namespace Shopsys\ShopBundle\Model\Product\Availability;

use Doctrine\ORM\EntityManager;
use Shopsys\ShopBundle\Component\Doctrine\EntityManagerFacade;
use Shopsys\ShopBundle\Model\Product\Product;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class ProductAvailabilityRecalculator
{
    const BATCH_SIZE = 100;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Shopsys\ShopBundle\Component\Doctrine\EntityManagerFacade
     */
    private $entityManagerFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Availability\ProductAvailabilityRecalculationScheduler
     */
    private $productAvailabilityRecalculationScheduler;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Availability\ProductAvailabilityCalculation
     */
    private $productAvailabilityCalculation;

    /**
     * @var \Doctrine\ORM\Internal\Hydration\IterableResult|\Shopsys\ShopBundle\Model\Product\Product[][]|null
     */
    private $productRowsIterator;

    public function __construct(
        EntityManager $em,
        EntityManagerFacade $entityManagerFacade,
        ProductAvailabilityRecalculationScheduler $productAvailabilityRecalculationScheduler,
        ProductAvailabilityCalculation $productAvailabilityCalculation
    ) {
        $this->em = $em;
        $this->entityManagerFacade = $entityManagerFacade;
        $this->productAvailabilityRecalculationScheduler = $productAvailabilityRecalculationScheduler;
        $this->productAvailabilityCalculation = $productAvailabilityCalculation;
    }

    public function runAllScheduledRecalculations()
    {
        $this->productRowsIterator = null;
        // @codingStandardsIgnoreStart
        while ($this->runBatchOfScheduledDelayedRecalculations()) {
        }
        // @codingStandardsIgnoreEnd
    }

    /**
     * @return bool
     */
    public function runBatchOfScheduledDelayedRecalculations()
    {
        if ($this->productRowsIterator === null) {
            $this->productRowsIterator = $this->productAvailabilityRecalculationScheduler->getProductsIteratorForDelayedRecalculation();
        }

        for ($count = 0; $count < self::BATCH_SIZE; $count++) {
            $row = $this->productRowsIterator->next();
            if ($row === false) {
                $this->entityManagerFacade->clear();

                return false;
            }
            $this->recalculateProductAvailability($row[0]);
        }

        $this->entityManagerFacade->clear();

        return true;
    }

    public function runImmediateRecalculations()
    {
        $products = $this->productAvailabilityRecalculationScheduler->getProductsForImmediateRecalculation();
        foreach ($products as $product) {
            $this->recalculateProductAvailability($product);
        }
        $this->productAvailabilityRecalculationScheduler->cleanScheduleForImmediateRecalculation();
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product $product
     */
    private function recalculateProductAvailability(Product $product)
    {
        $calculatedAvailability = $this->productAvailabilityCalculation->calculateAvailability($product);
        $product->setCalculatedAvailability($calculatedAvailability);
        if ($product->isVariant()) {
            $this->recalculateProductAvailability($product->getMainVariant());
        }
        $this->em->flush($product);
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if ($event->isMasterRequest()) {
            $this->runImmediateRecalculations();
        }
    }
}
