<?php

namespace Shopsys\FrameworkBundle\Model\Product\Pricing\Exception;

use Exception;

class ProductBasePriceCalculationException extends Exception implements ProductPricingException
{
    /**
     * @param string $message
     * @param \Exception|null $previous
     */
    public function __construct($message = '', Exception $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
