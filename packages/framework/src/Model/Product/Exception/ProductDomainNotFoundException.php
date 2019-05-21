<?php

namespace Shopsys\FrameworkBundle\Model\Product\Exception;

use Exception;

class ProductDomainNotFoundException extends Exception implements ProductException
{
    /**
     * @param int|null $productId
     * @param int $domainId
     * @param \Exception|null $previous
     */
    public function __construct(?int $productId = null, int $domainId, ?Exception $previous = null)
    {
        $productDescription = $productId !== null ? sprintf('with ID %d', $productId) : 'without ID';
        $message = sprintf('ProductDomain for product %s and domain ID %d not found.', $productDescription, $domainId);

        parent::__construct($message, 0, $previous);
    }
}
