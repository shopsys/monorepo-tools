<?php

namespace Shopsys\ShopBundle\Component\Image\Config\Exception;

use Exception;
use Shopsys\ShopBundle\Component\Image\Config\Exception\ImageConfigException;

class ImageEntityConfigNotFoundException extends Exception implements ImageConfigException
{
    /**
     * @var string
     */
    private $entityClassOrName;

    /**
     * @param string $entityClassOrName
     * @param \Exception|null $previous
     */
    public function __construct($entityClassOrName, Exception $previous = null) {
        $this->entityClassOrName = $entityClassOrName;

        parent::__construct('Not found image config for entity "' . $entityClassOrName . '".', 0, $previous);
    }

    /**
     * @return string
     */
    public function getEntityClassOrName() {
        return $this->entityClassOrName;
    }
}
