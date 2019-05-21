<?php

namespace Shopsys\FrameworkBundle\Model\Product\Brand\Exception;

use Exception;

class BrandDomainNotFoundException extends Exception implements BrandException
{
    /**
     * @param int|null $brandId
     * @param int $domainId
     * @param \Exception|null $previous
     */
    public function __construct(?int $brandId = null, int $domainId, ?Exception $previous = null)
    {
        $brandDescription = $brandId !== null ? sprintf('with ID %d', $brandId) : 'without ID';
        $message = sprintf('BrandDomain for brand %s and domain ID %d not found.', $brandDescription, $domainId);

        parent::__construct($message, 0, $previous);
    }
}
