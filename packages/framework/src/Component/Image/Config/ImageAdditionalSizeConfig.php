<?php

namespace Shopsys\FrameworkBundle\Component\Image\Config;

class ImageAdditionalSizeConfig
{
    /**
     * @var int|null
     */
    protected $width;

    /**
     * @var int|null
     */
    protected $height;

    /**
     * @var string
     */
    protected $media;

    /**
     * @param int|null $width
     * @param int|null $height
     * @param string $media
     */
    public function __construct(?int $width, ?int $height, string $media)
    {
        $this->width = $width;
        $this->height = $height;
        $this->media = $media;
    }

    /**
     * @return int|null
     */
    public function getWidth(): ?int
    {
        return $this->width;
    }

    /**
     * @return int|null
     */
    public function getHeight(): ?int
    {
        return $this->height;
    }

    /**
     * @return string
     */
    public function getMedia(): string
    {
        return $this->media;
    }
}
