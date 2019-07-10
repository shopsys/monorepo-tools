<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Model\Localization\Exception;

use Exception;

class UndefinedLegacyCurrencyException extends Exception implements LocalizationException
{
    /**
     * @param string $currencyCode
     * @param \Exception|null $previous
     */
    public function __construct(string $currencyCode, ?Exception $previous = null)
    {
        $message = sprintf('Legacy currency for code %s is not defined', $currencyCode);
        parent::__construct($message, 0, $previous);
    }
}
