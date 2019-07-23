<?php

declare(strict_types=1);

namespace Shopsys\BackendApiBundle\Controller\V1\Product;

use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\FrameworkBundle\Model\Product\ProductData;

/**
 * @experimental
 */
interface ProductDataFactoryInterface
{
    /**
     * @param array $productApiData
     * @param string|null $uuid
     * @return \Shopsys\FrameworkBundle\Model\Product\ProductData
     */
    public function createFromApi(array $productApiData, ?string $uuid = null): ProductData;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param array $productApiData
     * @return \Shopsys\FrameworkBundle\Model\Product\ProductData
     */
    public function createFromProductAndApi(Product $product, array $productApiData): ProductData;
}
