<?php

namespace Shopsys\FrameworkBundle\Model\Product\Availability;

use Doctrine\ORM\EntityManagerInterface;
use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\FrameworkBundle\Model\Product\ProductRepository;
use Shopsys\FrameworkBundle\Model\Product\ProductSellingDeniedRecalculator;
use Shopsys\FrameworkBundle\Model\Product\ProductVisibilityFacade;

class ProductAvailabilityCalculation
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Availability\AvailabilityFacade
     */
    protected $availabilityFacade;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductSellingDeniedRecalculator $productSellingDeniedRecalculator
     */
    protected $productSellingDeniedRecalculator;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductVisibilityFacade
     */
    protected $productVisibilityFacade;

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $em;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductRepository
     */
    protected $productRepository;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Availability\AvailabilityFacade $availabilityFacade
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductSellingDeniedRecalculator $productSellingDeniedRecalculator
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductVisibilityFacade $productVisibilityFacade
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductRepository $productRepository
     */
    public function __construct(
        AvailabilityFacade $availabilityFacade,
        ProductSellingDeniedRecalculator $productSellingDeniedRecalculator,
        ProductVisibilityFacade $productVisibilityFacade,
        EntityManagerInterface $em,
        ProductRepository $productRepository
    ) {
        $this->availabilityFacade = $availabilityFacade;
        $this->productSellingDeniedRecalculator = $productSellingDeniedRecalculator;
        $this->em = $em;
        $this->productVisibilityFacade = $productVisibilityFacade;
        $this->productRepository = $productRepository;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @return \Shopsys\FrameworkBundle\Model\Product\Availability\Availability
     */
    public function calculateAvailability(Product $product)
    {
        // If the product is not managed by EntityManager yet, it's not possible to calculate its availability consistently
        // Let's return a default availability for the moment and mark the product for recalculation
        if (!$this->em->contains($product)) {
            $product->markForAvailabilityRecalculation();

            return $this->availabilityFacade->getDefaultInStockAvailability();
        }

        if ($product->isMainVariant()) {
            return $this->calculateMainVariantAvailability($product);
        }
        if ($product->isUsingStock()) {
            if ($product->getStockQuantity() <= 0
                && $product->getOutOfStockAction() === Product::OUT_OF_STOCK_ACTION_SET_ALTERNATE_AVAILABILITY
            ) {
                return $product->getOutOfStockAvailability();
            } else {
                return $this->availabilityFacade->getDefaultInStockAvailability();
            }
        } else {
            return $product->getAvailability();
        }
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $mainVariant
     * @return \Shopsys\FrameworkBundle\Model\Product\Availability\Availability
     */
    protected function calculateMainVariantAvailability(Product $mainVariant)
    {
        $atLeastSomewhereSellableVariants = $this->getAtLeastSomewhereSellableVariantsByMainVariant($mainVariant);
        if (count($atLeastSomewhereSellableVariants) === 0) {
            return $this->availabilityFacade->getDefaultInStockAvailability();
        }
        $fastestAvailability = $this->calculateAvailability(array_shift($atLeastSomewhereSellableVariants));

        foreach ($atLeastSomewhereSellableVariants as $variant) {
            $variantCalculatedAvailability = $this->calculateAvailability($variant);
            if ($fastestAvailability->getDispatchTime() === null
                || $variantCalculatedAvailability->getDispatchTime() !== null
                && $variantCalculatedAvailability->getDispatchTime() < $fastestAvailability->getDispatchTime()
            ) {
                $fastestAvailability = $variantCalculatedAvailability;
            }
        }

        return $fastestAvailability;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $mainVariant
     * @return \Shopsys\FrameworkBundle\Model\Product\Product[]
     */
    protected function getAtLeastSomewhereSellableVariantsByMainVariant(Product $mainVariant)
    {
        $allVariants = $mainVariant->getVariants();
        foreach ($allVariants as $variant) {
            $this->productSellingDeniedRecalculator->calculateSellingDeniedForProduct($variant);
            $variant->markForVisibilityRecalculation();
        }
        $this->em->flush($allVariants);
        $this->productVisibilityFacade->refreshProductsVisibilityForMarked();

        return $this->productRepository->getAtLeastSomewhereSellableVariantsByMainVariant($mainVariant);
    }
}
