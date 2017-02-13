<?php

namespace Shopsys\ShopBundle\Component\Image\Config;

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
     * @param string|null $name
     * @param int $width
     * @param int $height
     * @param bool $crop
     */
    public function __construct($name, $width, $height, $crop)
    {
        $this->name = $name;
        $this->width = $width;
        $this->height = $height;
        $this->crop = $crop;
    }

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
}
