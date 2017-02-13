<?php

namespace Shopsys\ShopBundle\Model\Localization\Exception;

use Exception;

class UnsupportedCurrencyException extends Exception implements LocalizationException
{
    /**
     * @param string $currencyCode
     * @param \Exception|null $previous
     */
    public function __construct($currencyCode, Exception $previous = null)
    {
        $message = sprintf('Currency code %s is not supported', $currencyCode);
        parent::__construct($message, 0, $previous);
    }
}
