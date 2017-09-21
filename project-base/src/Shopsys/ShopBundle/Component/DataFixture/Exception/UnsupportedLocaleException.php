<?php

namespace Shopsys\ShopBundle\Component\DataFixture\Exception;

use Exception;

class UnsupportedLocaleException extends Exception implements DataFixtureException
{
    /**
     * @param string $locale
     * @param \Exception|null $previous
     */
    public function __construct($locale, Exception $previous = null)
    {
        parent::__construct('Locale "' . $locale . '" is not supported.', 0, $previous);
    }
}
