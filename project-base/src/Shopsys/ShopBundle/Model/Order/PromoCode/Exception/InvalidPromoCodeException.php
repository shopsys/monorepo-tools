<?php

namespace Shopsys\ShopBundle\Model\Order\PromoCode\Exception;

use Exception;

class InvalidPromoCodeException extends Exception implements PromoCodeException
{
    /**
     * @param string $invalidPromoCode
     * @param \Exception|null $previous
     */
    public function __construct($invalidPromoCode, Exception $previous = null)
    {
        parent::__construct('Promo code "' . $invalidPromoCode . '" is not valid.', 0, $previous);
    }
}
