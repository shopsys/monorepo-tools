<?php

namespace Shopsys\FrameworkBundle\Model\Product;

class ProductVariantService
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductFactoryInterface
     */
    protected $productFactory;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductFactoryInterface $productFactory
     */
    public function __construct(ProductFactoryInterface $productFactory)
    {
        $this->productFactory = $productFactory;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     */
    public function checkProductIsNotMainVariant(Product $product)
    {
        if ($product->isMainVariant()) {
            throw new \Shopsys\FrameworkBundle\Model\Product\Exception\ProductIsAlreadyMainVariantException($product->getId());
        }
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $mainProduct
     * @param \Shopsys\FrameworkBundle\Model\Product\Product[] $currentVariants
     */
    public function refreshProductVariants(Product $mainProduct, array $currentVariants)
    {
        $this->unsetRemovedVariants($mainProduct, $currentVariants);
        $this->addNewVariants($mainProduct, $currentVariants);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $mainProduct
     * @param \Shopsys\FrameworkBundle\Model\Product\Product[] $currentVariants
     */
    private function unsetRemovedVariants(Product $mainProduct, array $currentVariants)
    {
        foreach ($mainProduct->getVariants() as $originalVariant) {
            if (!in_array($originalVariant, $currentVariants, true)) {
                $originalVariant->unsetMainVariant();
            }
        }
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $mainProduct
     * @param \Shopsys\FrameworkBundle\Model\Product\Product[] $currentVariants
     */
    private function addNewVariants(Product $mainProduct, array $currentVariants)
    {
        foreach ($currentVariants as $currentVariant) {
            if (!in_array($currentVariant, $mainProduct->getVariants(), true)) {
                $mainProduct->addVariant($currentVariant);
            }
        }
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductData $mainVariantData
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $mainProduct
     * @param \Shopsys\FrameworkBundle\Model\Product\Product[] $variants
     * @return \Shopsys\FrameworkBundle\Model\Product\Product
     */
    public function createMainVariant(ProductData $mainVariantData, Product $mainProduct, array $variants)
    {
        $variants[] = $mainProduct;

        return $this->productFactory->createMainVariant($mainVariantData, $variants);
    }
}
