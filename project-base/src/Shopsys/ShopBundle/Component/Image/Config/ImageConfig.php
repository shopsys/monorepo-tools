<?php

namespace Shopsys\ShopBundle\Component\Image\Config;

use Shopsys\ShopBundle\Component\Image\Image;

class ImageConfig
{
    const ORIGINAL_SIZE_NAME = 'original';
    const DEFAULT_SIZE_NAME = 'default';

    /**
     * @var \Shopsys\ShopBundle\Component\Image\Config\ImageEntityConfig[]
     */
    private $imageEntityConfigsByClass;

    /**
     * @param \Shopsys\ShopBundle\Component\Image\Config\ImageEntityConfig[] $imageEntityConfigsByClass
     */
    public function __construct(array $imageEntityConfigsByClass) {
        $this->imageEntityConfigsByClass = $imageEntityConfigsByClass;
    }

    /**
     * @param Object $entity
     * @return string
     */
    public function getEntityName($entity) {
        $entityConfig = $this->getImageEntityConfig($entity);
        return $entityConfig->getEntityName();
    }

    /**
     * @param Object $entity
     * @param string|null $type
     * @param string|null $sizeName
     * @return \Shopsys\ShopBundle\Component\Image\Config\ImageSizeConfig
     */
    public function getImageSizeConfigByEntity($entity, $type, $sizeName) {
        $entityConfig = $this->getImageEntityConfig($entity);
        return $entityConfig->getSizeConfigByType($type, $sizeName);
    }

    /**
     * @param string $entityName
     * @param string|null $type
     * @param string|null $sizeName
     * @return \Shopsys\ShopBundle\Component\Image\Config\ImageSizeConfig
     */
    public function getImageSizeConfigByEntityName($entityName, $type, $sizeName) {
        $entityConfig = $this->getEntityConfigByEntityName($entityName);
        return $entityConfig->getSizeConfigByType($type, $sizeName);
    }

    /**
     * @param string $entityName
     * @param string|null $type
     * @param string|null $sizeName
     */
    public function assertImageSizeConfigByEntityNameExists($entityName, $type, $sizeName) {
        $this->getImageSizeConfigByEntityName($entityName, $type, $sizeName);
    }

    /**
     * @param \Shopsys\ShopBundle\Component\Image\Image $image
     * @param string|null $sizeName
     * @return \Shopsys\ShopBundle\Component\Image\Config\ImageSizeConfig
     */
    public function getImageSizeConfigByImage(Image $image, $sizeName) {
        $entityConfig = $this->getEntityConfigByEntityName($image->getEntityName());
        return $entityConfig->getSizeConfigByType($image->getType(), $sizeName);
    }

    /**
     * @param Object $entity
     * @return \Shopsys\ShopBundle\Component\Image\Config\ImageEntityConfig
     */
    public function getImageEntityConfig($entity) {
        foreach ($this->imageEntityConfigsByClass as $className => $entityConfig) {
            if ($entity instanceof $className) {
                return $entityConfig;
            }
        }

        throw new \Shopsys\ShopBundle\Component\Image\Config\Exception\ImageEntityConfigNotFoundException(
            $entity ? get_class($entity) : null
        );
    }

    /**
     * @param object $entity
     * @return bool
     */
    public function hasImageConfig($entity) {
        foreach ($this->imageEntityConfigsByClass as $className => $entityConfig) {
            if ($entity instanceof $className) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param string $entityName
     * @return \Shopsys\ShopBundle\Component\Image\Config\ImageEntityConfig;
     */
    public function getEntityConfigByEntityName($entityName) {
        foreach ($this->imageEntityConfigsByClass as $entityConfig) {
            if ($entityConfig->getEntityName() === $entityName) {
                return $entityConfig;
            }
        }

        throw new \Shopsys\ShopBundle\Component\Image\Config\Exception\ImageEntityConfigNotFoundException($entityName);
    }

    /**
     * @return \Shopsys\ShopBundle\Component\Image\Config\ImageEntityConfig[class]
     */
    public function getAllImageEntityConfigsByClass() {
        return $this->imageEntityConfigsByClass;
    }

    /**
     * @param string $class
     * @return \Shopsys\ShopBundle\Component\Image\Config\ImageEntityConfig
     */
    public function getImageEntityConfigByClass($class) {
        if (array_key_exists($class, $this->imageEntityConfigsByClass)) {
            return $this->imageEntityConfigsByClass[$class];
        }

        throw new \Shopsys\ShopBundle\Component\Image\Config\Exception\ImageEntityConfigNotFoundException($class);
    }
}
