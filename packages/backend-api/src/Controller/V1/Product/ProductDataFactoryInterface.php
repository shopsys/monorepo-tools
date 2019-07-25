<?php

declare(strict_types=1);

namespace Shopsys\BackendApiBundle\Controller\V1\Product;

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
}
