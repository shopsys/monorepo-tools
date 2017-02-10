<?php

namespace Shopsys\ShopBundle\Component\Image\Exception;

use Exception;

class EntityIdentifierException extends Exception implements ImageException {

    /**
     * @param string $message
     * @param \Exception|null $previous
     */
    public function __construct($message = '', Exception $previous = null) {
        parent::__construct($message, 0, $previous);
    }

}
