<?php

namespace Shopsys\ShopBundle\Twig\Exception;

use Exception;
use InvalidArgumentException as BaseInvalidArgumentException;
use Shopsys\ShopBundle\Twig\Exception\TwigException;

class InvalidArgumentException extends BaseInvalidArgumentException implements TwigException {

    /**
     * @param string $message
     * @param \Exception|null $previous
     */
    public function __construct($message = '', Exception $previous = null) {
        parent::__construct($message, 0, $previous);
    }

}
