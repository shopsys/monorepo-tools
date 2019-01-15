<?php

namespace Shopsys\FrameworkBundle\Model\Product;

use Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver;
use Shopsys\FrameworkBundle\Model\Product\Availability\ProductAvailabilityCalculation;

class ProductFactory implements ProductFactoryInterface
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver
     */
    protected $entityNameResolver;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Availability\ProductAvailabilityCalculation
     */
    protected $productAvailabilityCalculation;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductCategoryDomainFactoryInterface
     */
    private $productCategoryDomainFactory;

    /**
     * @param \Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver $entityNameResolver
     * @param \Shopsys\FrameworkBundle\Model\Product\Availability\ProductAvailabilityCalculation $productAvailabilityCalculation
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductCategoryDomainFactoryInterface $productCategoryDomainFactory
     */
    public function __construct(
        EntityNameResolver $entityNameResolver,
        ProductAvailabilityCalculation $productAvailabilityCalculation,
        ProductCategoryDomainFactoryInterface $productCategoryDomainFactory
    ) {
        $this->entityNameResolver = $entityNameResolver;
        $this->productAvailabilityCalculation = $productAvailabilityCalculation;
        $this->productCategoryDomainFactory = $productCategoryDomainFactory;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductData $data
     * @return \Shopsys\FrameworkBundle\Model\Product\Product
     */
    public function create(ProductData $data): Product
    {
        $classData = $this->entityNameResolver->resolve(Product::class);

        $product = $classData::create($data, $this->productCategoryDomainFactory);
        $this->setCalculatedAvailabilityIfMissing($product);

        return $product;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductData $data
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $mainProduct
     * @param \Shopsys\FrameworkBundle\Model\Product\Product[] $variants
     * @return \Shopsys\FrameworkBundle\Model\Product\Product
     */
    public function createMainVariant(ProductData $data, Product $mainProduct, array $variants): Product
    {
        $variants[] = $mainProduct;

        $classData = $this->entityNameResolver->resolve(Product::class);

        $mainVariant = $classData::createMainVariant($data, $this->productCategoryDomainFactory, $variants);
        $this->setCalculatedAvailabilityIfMissing($mainVariant);

        return $mainVariant;
    }

    /**
     * When creating new product, ProductData::$availability is set to Product::$calculatedAvailability property as well.
     * This is a problem when ProductData::$availability === null as $calculatedAvailability is not nullable (unlike $availability).
     * @see \Shopsys\FrameworkBundle\Model\Product\Product::__construct()
     *
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     */
    protected function setCalculatedAvailabilityIfMissing(Product $product)
    {
        if ($product->getCalculatedAvailability() === null) {
            $availability = $this->productAvailabilityCalculation->calculateAvailability($product);
            $product->setCalculatedAvailability($availability);
        }
    }
}
