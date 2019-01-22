<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Component\Image;

class AdditionalImageData
{
    /**
     * @var string
     */
    public $media;

    /**
     * @var string
     */
    public $url;

    /**
     * @param string $media
     * @param string $url
     */
    public function __construct(string $media, string $url)
    {
        $this->media = $media;
        $this->url = $url;
    }
}
