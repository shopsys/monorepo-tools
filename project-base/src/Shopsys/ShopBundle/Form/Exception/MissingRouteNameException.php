<?php

namespace Shopsys\ShopBundle\Form\Exception;

use Exception;
use Shopsys\ShopBundle\Form\Exception\FormException;

class MissingRouteNameException extends Exception implements FormException
{

    /**
     * @param string $message
     * @param \Exception|null $previous
     */
    public function __construct($message = '', Exception $previous = null) {
        parent::__construct($message, 0, $previous);
    }
}
