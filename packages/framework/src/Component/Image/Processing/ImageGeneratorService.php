<?php

namespace Shopsys\FrameworkBundle\Component\Image\Processing;

use Shopsys\FrameworkBundle\Component\Image\Config\ImageConfig;
use Shopsys\FrameworkBundle\Component\Image\Image;
use Shopsys\FrameworkBundle\Component\Image\ImageLocator;

class ImageGeneratorService
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Image\Processing\ImageProcessingService
     */
    private $imageProcessingService;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Image\ImageLocator
     */
    private $imageLocator;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Image\Config\ImageConfig
     */
    private $imageConfig;

    public function __construct(
        ImageProcessingService $imageProcessingService,
        ImageLocator $imageLocator,
        ImageConfig $imageConfig
    ) {
        $this->imageProcessingService = $imageProcessingService;
        $this->imageLocator = $imageLocator;
        $this->imageConfig = $imageConfig;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Image\Image $image
     * @param string|null $sizeName
     * @return string
     */
    public function generateImageSizeAndGetFilepath(Image $image, $sizeName)
    {
        if ($sizeName === ImageConfig::ORIGINAL_SIZE_NAME) {
            throw new \Shopsys\FrameworkBundle\Component\Image\Processing\Exception\OriginalSizeImageCannotBeGeneratedException(
                $image
            );
        }

        $sourceImageFilepath = $this->imageLocator->getAbsoluteImageFilepath($image, ImageConfig::ORIGINAL_SIZE_NAME);
        $targetImageFilepath = $this->imageLocator->getAbsoluteImageFilepath($image, $sizeName);
        $sizeConfig = $this->imageConfig->getImageSizeConfigByImage($image, $sizeName);

        $interventionImage = $this->imageProcessingService->createInterventionImage($sourceImageFilepath);
        $this->imageProcessingService->resizeBySizeConfig($interventionImage, $sizeConfig);
        $interventionImage->save($targetImageFilepath);

        return $targetImageFilepath;
    }
}
