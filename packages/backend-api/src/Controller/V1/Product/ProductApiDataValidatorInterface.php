<?php

declare(strict_types=1);

namespace Shopsys\BackendApiBundle\Controller\V1\Product;

/**
 * @experimental
 */
interface ProductApiDataValidatorInterface
{
    /**
     * @param array $productApiData
     * @return string[]
     */
    public function validateCreate(array $productApiData): array;
}
