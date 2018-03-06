<?php

namespace Shopsys\FrameworkBundle\Twig\FileThumbnail;

class FileThumbnailInfo
{
    /**
     * @var string|null
     */
    private $iconType;

    /**
     * @var string|null
     */
    private $imageUri;

    /**
     * @param string|null $iconType
     * @param string|null $imageUri
     */
    public function __construct($iconType, $imageUri = null)
    {
        $this->iconType = $iconType;
        $this->imageUri = $imageUri;
    }

    /**
     * @return string|null
     */
    public function getIconType()
    {
        return $this->iconType;
    }

    /**
     * @return string|null
     */
    public function getImageUri()
    {
        return $this->imageUri;
    }
}
