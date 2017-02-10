<?php

namespace Shopsys\ShopBundle\Model\Product\Availability;

use Doctrine\ORM\EntityManager;
use Shopsys\ShopBundle\Model\Product\Availability\AvailabilityFacade;
use Shopsys\ShopBundle\Model\Product\Product;
use Shopsys\ShopBundle\Model\Product\ProductRepository;
use Shopsys\ShopBundle\Model\Product\ProductSellingDeniedRecalculator;
use Shopsys\ShopBundle\Model\Product\ProductVisibilityFacade;

class ProductAvailabilityCalculation
{

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Availability\AvailabilityFacade
     */
    private $availabilityFacade;

    /**
     * @param \Shopsys\ShopBundle\Model\Product\ProductSellingDeniedRecalculator $productSellingDeniedRecalculator
     */
    private $productSellingDeniedRecalculator;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\ProductVisibilityFacade
     */
    private $productVisibilityFacade;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\ProductRepository
     */
    private $productRepository;

    public function __construct(
        AvailabilityFacade $availabilityFacade,
        ProductSellingDeniedRecalculator $productSellingDeniedRecalculator,
        ProductVisibilityFacade $productVisibilityFacade,
        EntityManager $em,
        ProductRepository $productRepository
    ) {
        $this->availabilityFacade = $availabilityFacade;
        $this->productSellingDeniedRecalculator = $productSellingDeniedRecalculator;
        $this->em = $em;
        $this->productVisibilityFacade = $productVisibilityFacade;
        $this->productRepository = $productRepository;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product $product
     * @return \Shopsys\ShopBundle\Model\Product\Availability\Availability
     */
    public function calculateAvailability(Product $product) {
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
     * @param \Shopsys\ShopBundle\Model\Product\Product $mainVariant
     * @return \Shopsys\ShopBundle\Model\Product\Availability\Availability
     */
    private function calculateMainVariantAvailability(Product $mainVariant) {
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
     * @param \Shopsys\ShopBundle\Model\Product\Product $mainVariant
     * @return \Shopsys\ShopBundle\Model\Product\Product[]
     */
    private function getAtLeastSomewhereSellableVariantsByMainVariant(Product $mainVariant) {
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
