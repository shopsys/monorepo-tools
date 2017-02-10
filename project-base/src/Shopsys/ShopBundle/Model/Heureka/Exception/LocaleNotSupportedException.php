<?php

namespace Shopsys\ShopBundle\Model\Heureka\Exception;

use Exception;
use Shopsys\ShopBundle\Model\Heureka\Exception\HeurekaException;

class LocaleNotSupportedException extends Exception implements HeurekaException {

    /**
     * @param string $message
     * @param \Exception|null $previous
     */
    public function __construct($message = '', Exception $previous = null) {
        parent::__construct($message, 0, $previous);
    }
}
