<?php

namespace Shopsys\ShopBundle\Component\Image\Processing;

use Shopsys\ShopBundle\Component\Image\Config\ImageConfig;
use Shopsys\ShopBundle\Component\Image\Image;
use Shopsys\ShopBundle\Component\Image\ImageLocator;
use Shopsys\ShopBundle\Component\Image\Processing\Exception\OriginalSizeImageCannotBeGeneratedException;
use Shopsys\ShopBundle\Component\Image\Processing\ImageProcessingService;

class ImageGeneratorService
{
    /**
     * @var \Shopsys\ShopBundle\Component\Image\Processing\ImageProcessingService
     */
    private $imageProcessingService;

    /**
     * @var \Shopsys\ShopBundle\Component\Image\ImageLocator
     */
    private $imageLocator;

    /**
     * @var \Shopsys\ShopBundle\Component\Image\Config\ImageConfig
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
     * @param \Shopsys\ShopBundle\Component\Image\Image $image
     * @param string|null $sizeName
     * @return string
     */
    public function generateImageSizeAndGetFilepath(Image $image, $sizeName)
    {
        if ($sizeName === ImageConfig::ORIGINAL_SIZE_NAME) {
            throw new \Shopsys\ShopBundle\Component\Image\Processing\Exception\OriginalSizeImageCannotBeGeneratedException(
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
