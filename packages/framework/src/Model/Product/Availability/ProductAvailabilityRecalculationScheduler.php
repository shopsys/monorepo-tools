<?php

namespace Shopsys\FrameworkBundle\Model\Product\Availability;

use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\FrameworkBundle\Model\Product\ProductRepository;

class ProductAvailabilityRecalculationScheduler
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductRepository
     */
    private $productRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Product[]
     */
    private $products = [];

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductRepository $productRepository
     */
    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     */
    public function scheduleProductForImmediateRecalculation(Product $product)
    {
        $this->products[$product->getId()] = $product;
    }

    public function scheduleAllProductsForDelayedRecalculation()
    {
        $this->productRepository->markAllProductsForAvailabilityRecalculation();
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\Product[]
     */
    public function getProductsForImmediateRecalculation()
    {
        return $this->products;
    }

    /**
     * @return \Doctrine\ORM\Internal\Hydration\IterableResult|\Shopsys\FrameworkBundle\Model\Product\Product[][]
     */
    public function getProductsIteratorForDelayedRecalculation()
    {
        return $this->productRepository->getProductsForAvailabilityRecalculationIterator();
    }

    public function cleanScheduleForImmediateRecalculation()
    {
        $this->products = [];
    }
}
