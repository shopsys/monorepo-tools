<?php

namespace Shopsys\ShopBundle\Component\Image\Config\Exception;

use Exception;
use Shopsys\ShopBundle\Component\Image\Config\Exception\ImageConfigException;

class ImageSizeNotFoundException extends Exception implements ImageConfigException
{
    /**
     * @var string
     */
    private $entityClass;

    /**
     * @var string
     */
    private $sizeName;

    /**
     * @param string $entityClass
     * @param string $sizeName
     * @param \Exception|null $previous
     */
    public function __construct($entityClass, $sizeName, Exception $previous = null) {
        $this->entityClass = $entityClass;
        $this->sizeName = $sizeName;

        parent::__construct('Image size "' . $sizeName . '" not found for entity "' . $entityClass . '".', 0, $previous);
    }

    /**
     * @return string
     */
    public function getEntityClass() {
        return $this->entityClass;
    }

    /**
     * @return string
     */
    public function getSizeName() {
        return $this->sizeName;
    }
}
