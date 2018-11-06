<?php

namespace Shopsys\FrameworkBundle\Model\Product\Collection\Exception;

use Exception;
use Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig;
use Shopsys\FrameworkBundle\Model\Product\Product;

class ProductUrlNotLoadedException extends Exception implements ProductCollectionException
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @param \Exception|null $previous
     */
    public function __construct(Product $product, DomainConfig $domainConfig, Exception $previous = null)
    {
        $message = sprintf(
            'URL for product with ID %d on %s have not been loaded via ProductUrlsBatchLoader::loadForProducts().',
            $product->getId(),
            $domainConfig->getName()
        );
        parent::__construct($message, 0, $previous);
    }
}
