<?php

namespace Shopsys\FrameworkBundle\Model\Product;

class ProductVariantService
{
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
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductEditData $mainVariantEditData
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $mainProduct
     * @param \Shopsys\FrameworkBundle\Model\Product\Product[] $variants
     * @return \Shopsys\FrameworkBundle\Model\Product\Product
     */
    public function createMainVariant(ProductEditData $mainVariantEditData, Product $mainProduct, array $variants)
    {
        $variants[] = $mainProduct;

        return Product::createMainVariant($mainVariantEditData->productData, $variants);
    }
}
