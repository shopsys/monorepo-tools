<?php

namespace Shopsys\ShopBundle\Component\FlashMessage\Exception;

use Exception;

class BagNameIsNotValidException extends Exception implements FlashMessageException {

    /**
     * @param string $message
     * @param \Exception|null $previous
     */
    public function __construct($message = '', Exception $previous = null) {
        parent::__construct($message, 0, $previous);
    }
}
