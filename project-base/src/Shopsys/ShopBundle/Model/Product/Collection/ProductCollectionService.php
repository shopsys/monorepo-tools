<?php

namespace Shopsys\ShopBundle\Model\Product\Collection;

class ProductCollectionService
{
    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product $products
     * @param \Shopsys\ShopBundle\Component\Image\Image $imagesByProductId
     * @return array
     */
    public function getImagesIndexedByProductId(array $products, array $imagesByProductId) {
        $imagesOrNullByProductId = [];

        foreach ($products as $product) {
            if (array_key_exists($product->getId(), $imagesByProductId)) {
                $imagesOrNullByProductId[$product->getId()] = $imagesByProductId[$product->getId()];
            } else {
                $imagesOrNullByProductId[$product->getId()] = null;
            }
        }

        return $imagesOrNullByProductId;
    }
}
