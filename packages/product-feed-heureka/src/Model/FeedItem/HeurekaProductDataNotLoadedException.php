<?php

namespace Shopsys\ProductFeed\HeurekaBundle\Model\FeedItem;

use Exception;
use Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig;
use Shopsys\FrameworkBundle\Model\Product\Product;

class HeurekaProductDataNotLoadedException extends Exception
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @param string $attribute
     * @param \Exception|null $previous
     */
    public function __construct(Product $product, DomainConfig $domainConfig, string $attribute, Exception $previous = null)
    {
        $message = sprintf(
            '%s of product with ID %d on %s have not been loaded via HeurekaProductDataBatchLoader::loadForProducts().',
            ucfirst($attribute),
            $product->getId(),
            $domainConfig->getId()
        );

        parent::__construct($message, 0, $previous);
    }
}
