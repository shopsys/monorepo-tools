<?php

namespace Shopsys\ShopBundle\Model\Product\Pricing;

use Shopsys\ShopBundle\Model\Product\Product;
use Shopsys\ShopBundle\Model\Product\ProductRepository;

class ProductPriceRecalculationScheduler
{

    /**
     * @var \Shopsys\ShopBundle\Model\Product\ProductRepository
     */
    private $productRepository;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Product[]
     */
    private $products = [];

    /**
     * @param \Shopsys\ShopBundle\Model\Product\ProductRepository $productRepository
     */
    public function __construct(ProductRepository $productRepository) {
        $this->productRepository = $productRepository;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product $product
     */
    public function scheduleProductForImmediateRecalculation(Product $product) {
        $this->products[$product->getId()] = $product;
    }

    public function scheduleAllProductsForDelayedRecalculation() {
        $this->productRepository->markAllProductsForPriceRecalculation();
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Product\Product[]
     */
    public function getProductsForImmediateRecalculation() {
        return $this->products;
    }

    /**
     * @return \Doctrine\ORM\Internal\Hydration\IterableResult|\Shopsys\ShopBundle\Model\Product\Product[][0]
     */
    public function getProductsIteratorForDelayedRecalculation() {
        return $this->productRepository->getProductsForPriceRecalculationIterator();
    }

    public function cleanScheduleForImmediateRecalculation() {
        $this->products = [];
    }
}
