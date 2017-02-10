<?php

namespace Shopsys\ShopBundle\Component\Domain\Config\Exception;

use Exception;

class DomainConfigsDoNotMatchException extends Exception implements DomainConfigException
{

    /**
     * @param string $message
     * @param \Exception|null $previous
     */
    public function __construct($message = '', $previous = null) {
        parent::__construct($message, 0, $previous);
    }
}
