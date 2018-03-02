<?php

namespace Shopsys\FrameworkBundle\Component\Image\Exception;

use Exception;

class ImageNotFoundException extends Exception implements ImageException
{
    /**
     * @param string $message
     * @param \Exception|null $previous
     */
    public function __construct($message = '', Exception $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
