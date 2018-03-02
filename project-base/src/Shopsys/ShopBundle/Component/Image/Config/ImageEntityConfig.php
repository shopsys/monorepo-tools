<?php

namespace Shopsys\FrameworkBundle\Component\Image\Config;

use Shopsys\FrameworkBundle\Component\Utils;

class ImageEntityConfig
{
    const WITHOUT_NAME_KEY = '__NULL__';

    /**
     * @var string
     */
    private $entityName;

    /**
     * @var string
     */
    private $entityClass;

    /**
     * @var array
     */
    private $sizeConfigsByType;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Image\Config\ImageSizeConfig[]
     */
    private $sizeConfigs;

    /**
     * @var array
     */
    private $multipleByType;

    /**
     * @param string $entityName
     * @param string $entityClass
     * @param array $sizeConfigsByType
     * @param \Shopsys\FrameworkBundle\Component\Image\Config\ImageSizeConfig[] $sizeConfigs
     * @param array $multipleByType
     */
    public function __construct($entityName, $entityClass, array $sizeConfigsByType, array $sizeConfigs, array $multipleByType)
    {
        $this->entityName = $entityName;
        $this->entityClass = $entityClass;
        $this->sizeConfigsByType = $sizeConfigsByType;
        $this->sizeConfigs = $sizeConfigs;
        $this->multipleByType = $multipleByType;
    }

    /**
     * @return string
     */
    public function getEntityName()
    {
        return $this->entityName;
    }

    /**
     * @return string
     */
    public function getEntityClass()
    {
        return $this->entityClass;
    }

    /**
     * @return array
     */
    public function getTypes()
    {
        return array_keys($this->sizeConfigsByType);
    }

    /**
     * @return \Shopsys\FrameworkBundle\Component\Image\Config\ImageSizeConfig[]
     */
    public function getSizeConfigs()
    {
        return $this->sizeConfigs;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Component\Image\Config\ImageSizeConfig[]
     */
    public function getSizeConfigsByTypes()
    {
        return $this->sizeConfigsByType;
    }

    /**
     * @param string $type
     * @return \Shopsys\FrameworkBundle\Component\Image\Config\ImageSizeConfig[]
     */
    public function getSizeConfigsByType($type)
    {
        if (array_key_exists($type, $this->sizeConfigsByType)) {
            return $this->sizeConfigsByType[$type];
        } else {
            throw new \Shopsys\FrameworkBundle\Component\Image\Config\Exception\ImageTypeNotFoundException($this->entityClass, $type);
        }
    }

    /**
     * @param string|null $sizeName
     * @return \Shopsys\FrameworkBundle\Component\Image\Config\ImageSizeConfig
     */
    public function getSizeConfig($sizeName)
    {
        return $this->getSizeConfigFromSizeConfigs($this->sizeConfigs, $sizeName);
    }

    /**
     * @param string|null $type
     * @param string|null $sizeName
     * @return \Shopsys\FrameworkBundle\Component\Image\Config\ImageSizeConfig
     */
    public function getSizeConfigByType($type, $sizeName)
    {
        if ($type === null) {
            $typeSizes = $this->sizeConfigs;
        } else {
            $typeSizes = $this->getSizeConfigsByType($type);
        }
        return $this->getSizeConfigFromSizeConfigs($typeSizes, $sizeName);
    }

    /**
     * @param string|null $type
     * @return bool
     */
    public function isMultiple($type)
    {
        $key = Utils::ifNull($type, self::WITHOUT_NAME_KEY);
        if (array_key_exists($key, $this->multipleByType)) {
            return $this->multipleByType[$key];
        } else {
            throw new \Shopsys\FrameworkBundle\Component\Image\Config\Exception\ImageTypeNotFoundException($this->entityClass, $type);
        }
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Image\Config\ImageSizeConfig[] $sizes
     * @param string $sizeName
     * @return \Shopsys\FrameworkBundle\Component\Image\Config\ImageSizeConfig
     */
    private function getSizeConfigFromSizeConfigs($sizes, $sizeName)
    {
        $key = Utils::ifNull($sizeName, self::WITHOUT_NAME_KEY);
        if (array_key_exists($key, $sizes)) {
            return $sizes[$key];
        } else {
            throw new \Shopsys\FrameworkBundle\Component\Image\Config\Exception\ImageSizeNotFoundException($this->entityClass, $sizeName);
        }
    }
}
