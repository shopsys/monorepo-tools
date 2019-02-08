<?php

namespace Shopsys\FrameworkBundle\Component\Image\Processing;

use League\Flysystem\FilesystemInterface;
use Shopsys\FrameworkBundle\Component\Image\Config\ImageConfig;
use Shopsys\FrameworkBundle\Component\Image\Image;
use Shopsys\FrameworkBundle\Component\Image\ImageLocator;
use Shopsys\FrameworkBundle\Component\Image\Processing\Exception\OriginalSizeImageCannotBeGeneratedException;

class ImageGenerator
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Image\Processing\ImageProcessor
     */
    protected $imageProcessor;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Image\ImageLocator
     */
    protected $imageLocator;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Image\Config\ImageConfig
     */
    protected $imageConfig;

    /**
     * @var \League\Flysystem\FilesystemInterface
     */
    protected $filesystem;

    /**
     * @param \Shopsys\FrameworkBundle\Component\Image\Processing\ImageProcessor $imageProcessor
     * @param \Shopsys\FrameworkBundle\Component\Image\ImageLocator $imageLocator
     * @param \Shopsys\FrameworkBundle\Component\Image\Config\ImageConfig $imageConfig
     * @param \League\Flysystem\FilesystemInterface $filesystem
     */
    public function __construct(
        ImageProcessor $imageProcessor,
        ImageLocator $imageLocator,
        ImageConfig $imageConfig,
        FilesystemInterface $filesystem
    ) {
        $this->imageProcessor = $imageProcessor;
        $this->imageLocator = $imageLocator;
        $this->imageConfig = $imageConfig;
        $this->filesystem = $filesystem;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Image\Image $image
     * @param string|null $sizeName
     * @return string
     */
    public function generateImageSizeAndGetFilepath(Image $image, $sizeName)
    {
        $this->checkSizeNameIsNotOriginal($image, $sizeName);

        $sourceImageFilepath = $this->imageLocator->getAbsoluteImageFilepath($image, ImageConfig::ORIGINAL_SIZE_NAME);
        $targetImageFilepath = $this->imageLocator->getAbsoluteImageFilepath($image, $sizeName);
        $sizeConfig = $this->imageConfig->getImageSizeConfigByImage($image, $sizeName);

        $interventionImage = $this->imageProcessor->createInterventionImage($sourceImageFilepath);
        $this->imageProcessor->resizeBySizeConfig($interventionImage, $sizeConfig);

        $interventionImage->encode();

        $this->filesystem->put($targetImageFilepath, $interventionImage);

        return $targetImageFilepath;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Image\Image $image
     * @param int $additionalIndex
     * @param string|null $sizeName
     * @return string
     */
    public function generateAdditionalImageSizeAndGetFilepath(Image $image, int $additionalIndex, ?string $sizeName)
    {
        $this->checkSizeNameIsNotOriginal($image, $sizeName);

        $sourceImageFilepath = $this->imageLocator->getAbsoluteImageFilepath($image, ImageConfig::ORIGINAL_SIZE_NAME);
        $targetImageFilepath = $this->imageLocator->getAbsoluteAdditionalImageFilepath($image, $additionalIndex, $sizeName);
        $sizeConfig = $this->imageConfig->getImageSizeConfigByImage($image, $sizeName);
        $additionalSizeConfig = $sizeConfig->getAdditionalSize($additionalIndex);

        $interventionImage = $this->imageProcessor->createInterventionImage($sourceImageFilepath);
        $this->imageProcessor->resizeByAdditionalSizeConfig($interventionImage, $sizeConfig, $additionalSizeConfig);

        $interventionImage->encode();

        $this->filesystem->put($targetImageFilepath, $interventionImage);

        return $targetImageFilepath;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Image\Image $image
     * @param string|null $sizeName
     */
    protected function checkSizeNameIsNotOriginal(Image $image, ?string $sizeName): void
    {
        if ($sizeName === ImageConfig::ORIGINAL_SIZE_NAME) {
            throw new OriginalSizeImageCannotBeGeneratedException($image);
        }
    }
}
