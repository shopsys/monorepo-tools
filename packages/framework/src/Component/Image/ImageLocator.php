<?php

namespace Shopsys\FrameworkBundle\Component\Image;

use Shopsys\FrameworkBundle\Component\Image\Config\ImageConfig;

class ImageLocator
{
    /**
     * @var string
     */
    private $imageDir;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Image\Config\ImageConfig
     */
    private $imageConfig;

    public function __construct($imageDir, ImageConfig $imageConfig)
    {
        $this->imageDir = $imageDir;
        $this->imageConfig = $imageConfig;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Image\Image $image
     * @param string|null $sizeName
     * @return string
     */
    public function getRelativeImageFilepath(Image $image, $sizeName)
    {
        $path = $this->getRelativeImagePath($image->getEntityName(), $image->getType(), $sizeName);

        return $path . $image->getFilename();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Image\Image $image
     * @param string|null $sizeName
     * @return string
     */
    public function getAbsoluteImageFilepath(Image $image, $sizeName)
    {
        $relativePath = $this->getRelativeImageFilepath($image, $sizeName);

        return $this->imageDir . $relativePath;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Image\Image $image
     * @return bool
     */
    public function imageExists(Image $image)
    {
        $imageFilepath = $this->getAbsoluteImageFilepath($image, ImageConfig::ORIGINAL_SIZE_NAME);

        return is_file($imageFilepath) && is_readable($imageFilepath);
    }

    /**
     * @param string $entityName
     * @param string|null $type
     * @param string|null $sizeName
     * @return string
     */
    public function getRelativeImagePath($entityName, $type, $sizeName)
    {
        $this->imageConfig->assertImageSizeConfigByEntityNameExists($entityName, $type, $sizeName);
        $pathParts = [$entityName];

        if ($type !== null) {
            $pathParts[] = $type;
        }
        if ($sizeName === null) {
            $pathParts[] = ImageConfig::DEFAULT_SIZE_NAME;
        } else {
            $pathParts[] = $sizeName;
        }

        return implode('/', $pathParts) . '/';
    }
}
