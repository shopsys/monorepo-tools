<?php

namespace Shopsys\FrameworkBundle\Component\Image\Config\Exception;

use Exception;
use Shopsys\FrameworkBundle\Component\Image\Config\ImageConfigDefinition;

class WidthAndHeightMissingException extends Exception implements ImageConfigException
{
    /**
     * @param string $additionalSizeName
     * @param \Exception|null $previous
     */
    public function __construct(string $additionalSizeName, ?Exception $previous = null)
    {
        $message = sprintf(
            'You have to specify at least one of "%s" or "%s" for additional size "%s"',
            ImageConfigDefinition::CONFIG_SIZE_WIDTH,
            ImageConfigDefinition::CONFIG_SIZE_HEIGHT,
            $additionalSizeName
        );

        parent::__construct($message, 0, $previous);
    }
}
