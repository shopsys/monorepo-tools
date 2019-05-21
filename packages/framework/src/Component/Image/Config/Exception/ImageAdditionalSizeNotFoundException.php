<?php

namespace Shopsys\FrameworkBundle\Component\Image\Config\Exception;

use Exception;

class ImageAdditionalSizeNotFoundException extends Exception implements ImageConfigException
{
    /**
     * @param string|null $sizeName
     * @param int $additionalIndex
     * @param \Exception|null $previous
     */
    public function __construct(?string $sizeName, int $additionalIndex, ?Exception $previous = null)
    {
        $sizeName = $sizeName ?: '~';
        $message = sprintf('Image size "%s" does not contain additional size on index "%s".', $sizeName, $additionalIndex);
        parent::__construct($message, 0, $previous);
    }
}
