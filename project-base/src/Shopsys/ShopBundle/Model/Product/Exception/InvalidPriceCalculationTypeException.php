<?php

namespace Shopsys\ShopBundle\Model\Product\Exception;

use Exception;

class InvalidPriceCalculationTypeException extends Exception implements ProductException
{

    /**
     * @param string $priceCalculationType
     * @param \Exception|null $previous
     */
    public function __construct($priceCalculationType, Exception $previous = null) {
        $message = 'Price calculation type "' . $priceCalculationType . '" is not valid.';
        parent::__construct($message, 0, $previous);
    }

}
