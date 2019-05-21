<?php

namespace Shopsys\FrameworkBundle\Component\Image\Config\Exception;

use Exception;

class DuplicateMediaException extends Exception implements ImageConfigException
{
    /**
     * @param string $media
     * @param \Exception|null $previous
     */
    public function __construct(string $media, ?Exception $previous = null)
    {
        $message = sprintf('Additional size media "%s" is not unique.', $media);
        parent::__construct($message, 0, $previous);
    }
}
