<?php

namespace Shopsys\FrameworkBundle\Model\Product\Pricing;

use Doctrine\ORM\EntityManagerInterface;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupFacade;
use Shopsys\FrameworkBundle\Model\Product\Product;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class ProductPriceRecalculator
{
    const BATCH_SIZE = 250;

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $em;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceCalculation
     */
    protected $productPriceCalculation;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductCalculatedPriceRepository
     */
    protected $productCalculatedPriceRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler
     */
    protected $productPriceRecalculationScheduler;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupFacade
     */
    protected $pricingGroupFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup[]|null
     */
    protected $allPricingGroups;

    /**
     * @var \Doctrine\ORM\Internal\Hydration\IterableResult|\Shopsys\FrameworkBundle\Model\Product\Product[][]|null
     */
    protected $productRowsIterator;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @param \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceCalculation $productPriceCalculation
     * @param \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductCalculatedPriceRepository $productCalculatedPriceRepository
     * @param \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler $productPriceRecalculationScheduler
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupFacade $pricingGroupFacade
     */
    public function __construct(
        EntityManagerInterface $em,
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
     * @return bool
     */
    public function runBatchOfScheduledDelayedRecalculations()
    {
        if ($this->productRowsIterator === null) {
            $this->productRowsIterator = $this->productPriceRecalculationScheduler->getProductsIteratorForDelayedRecalculation();
        }

        for ($count = 0; $count < self::BATCH_SIZE; $count++) {
            $row = $this->productRowsIterator->next();
            if ($row === false) {
                $this->clearCache();
                $this->em->clear();

                return false;
            }
            $this->recalculateProductPrices($row[0]);
        }
        $this->clearCache();
        $this->em->clear();

        return true;
    }

    public function runAllScheduledRecalculations()
    {
        $this->runImmediateRecalculations();

        $this->productRowsIterator = null;
        while ($this->runBatchOfScheduledDelayedRecalculations()) {
        }
    }

    public function runImmediateRecalculations()
    {
        $products = $this->productPriceRecalculationScheduler->getProductsForImmediateRecalculation();
        foreach ($products as $product) {
            $this->recalculateProductPrices($product);
        }
        $this->productPriceRecalculationScheduler->cleanScheduleForImmediateRecalculation();
        $this->clearCache();
    }

    protected function clearCache()
    {
        $this->allPricingGroups = null;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup[]
     */
    protected function getAllPricingGroups()
    {
        if ($this->allPricingGroups === null) {
            $this->allPricingGroups = $this->pricingGroupFacade->getAll();
        }

        return $this->allPricingGroups;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     */
    protected function recalculateProductPrices(Product $product)
    {
        foreach ($this->getAllPricingGroups() as $pricingGroup) {
            try {
                $price = $this->productPriceCalculation->calculatePrice($product, $pricingGroup->getDomainId(), $pricingGroup);
                $priceWithVat = $price->getPriceWithVat();
            } catch (\Shopsys\FrameworkBundle\Model\Product\Pricing\Exception\MainVariantPriceCalculationException $e) {
                $priceWithVat = null;
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
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if ($event->isMasterRequest()) {
            $this->runImmediateRecalculations();
        }
    }
}
