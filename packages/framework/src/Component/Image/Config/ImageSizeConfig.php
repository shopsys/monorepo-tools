<?php

namespace Shopsys\FrameworkBundle\Component\Image\Config;

class ImageSizeConfig
{
    /**
     * @var string|null
     */
    private $name;

    /**
     * @var int
     */
    private $width;

    /**
     * @var int
     */
    private $height;

    /**
     * @var bool
     */
    private $crop;

    /**
     * @var string|null
     */
    private $occurrence;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Image\Config\ImageAdditionalSizeConfig[]
     */
    private $additionalSizes;

    /**
     * @param string|null $name
     * @param int $width
     * @param int $height
     * @param bool $crop
     * @param string|null $occurrence
     * @param \Shopsys\FrameworkBundle\Component\Image\Config\ImageAdditionalSizeConfig[] $additionalSizes
     */
    public function __construct($name, $width, $height, $crop, $occurrence, array $additionalSizes)
    {
        $this->name = $name;
        $this->width = $width;
        $this->height = $height;
        $this->crop = $crop;
        $this->occurrence = $occurrence;
        $this->additionalSizes = $additionalSizes;
    }

    /**
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @return int
     */
    public function getCrop()
    {
        return $this->crop;
    }

    /**
     * @return string|null
     */
    public function getOccurrence()
    {
        return $this->occurrence;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Component\Image\Config\ImageAdditionalSizeConfig[]
     */
    public function getAdditionalSizes(): array
    {
        return $this->additionalSizes;
    }
}
