<?php

namespace Shopsys\ShopBundle\Component\Css\Exception;

use Exception;

class CssVersionFileNotFound extends Exception
{

    /**
     * @param string $message
     * @param \Exception|null $previous
     */
    public function __construct($message = '', $previous = null) {
        parent::__construct($message, 0, $previous);
    }
}
