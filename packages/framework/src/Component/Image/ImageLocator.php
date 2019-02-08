<?php

namespace Shopsys\FrameworkBundle\Component\Image;

use League\Flysystem\FilesystemInterface;
use Shopsys\FrameworkBundle\Component\Image\Config\ImageConfig;

class ImageLocator
{
    protected const ADDITIONAL_IMAGE_MASK = 'additional_{index}_{filename}';

    /**
     * @var string
     */
    protected $imageDir;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Image\Config\ImageConfig
     */
    protected $imageConfig;

    /**
     * @var \League\Flysystem\FilesystemInterface
     */
    protected $filesystem;

    /**
     * @param mixed $imageDir
     * @param \Shopsys\FrameworkBundle\Component\Image\Config\ImageConfig $imageConfig
     * @param \League\Flysystem\FilesystemInterface $filesystem
     */
    public function __construct($imageDir, ImageConfig $imageConfig, FilesystemInterface $filesystem)
    {
        $this->imageDir = $imageDir;
        $this->imageConfig = $imageConfig;
        $this->filesystem = $filesystem;
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
     * @param int $additionalIndex
     * @param string|null $sizeName
     * @return string
     */
    public function getRelativeAdditionalImageFilepath(Image $image, int $additionalIndex, ?string $sizeName)
    {
        $path = $this->getRelativeImagePath($image->getEntityName(), $image->getType(), $sizeName);

        $filename = str_replace(
            ['{index}', '{filename}'],
            [$additionalIndex, $image->getFilename()],
            self::ADDITIONAL_IMAGE_MASK
        );

        return $path . $filename;
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
     * @param int $additionalIndex
     * @param string|null $sizeName
     * @return string
     */
    public function getAbsoluteAdditionalImageFilepath(Image $image, int $additionalIndex, ?string $sizeName)
    {
        $relativePath = $this->getRelativeAdditionalImageFilepath($image, $additionalIndex, $sizeName);

        return $this->imageDir . $relativePath;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Image\Image $image
     * @return bool
     */
    public function imageExists(Image $image)
    {
        $imageFilepath = $this->getAbsoluteImageFilepath($image, ImageConfig::ORIGINAL_SIZE_NAME);

        return $this->filesystem->has($imageFilepath);
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
