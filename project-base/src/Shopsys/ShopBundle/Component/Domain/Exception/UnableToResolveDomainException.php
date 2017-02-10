<?php

namespace Shopsys\ShopBundle\Component\Domain\Exception;

use Exception;

class UnableToResolveDomainException extends Exception implements DomainException
{
    /**
     * @param string $url
     * @param \Exception|null $previous
     */
    public function __construct($url, $previous = null) {
        $message = sprintf('Unable to resolve domain for URL: %s', $url);

        parent::__construct($message, 0, $previous);
    }
}
