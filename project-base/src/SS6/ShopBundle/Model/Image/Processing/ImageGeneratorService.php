<?php

namespace SS6\ShopBundle\Model\Image\Processing;

use SS6\ShopBundle\Model\Image\Config\ImageConfig;
use SS6\ShopBundle\Model\Image\Image;
use SS6\ShopBundle\Model\Image\ImageLocator;
use SS6\ShopBundle\Model\Image\Processing\ImageProcessingService;

class ImageGeneratorService {

	/**
	 * @var \SS6\ShopBundle\Model\Image\Processing\ImageProcessingService
	 */
	private $imageProcessingService;

	/**
	 * @var \SS6\ShopBundle\Model\Image\ImageLocator
	 */
	private $imageLocator;

	/**
	 * @var \SS6\ShopBundle\Model\Image\Config\ImageConfig
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
	 * @param \SS6\ShopBundle\Model\Image\Image $image
	 * @param string|null $sizeName
	 * @return string
	 */
	public function generateImageSizeAndGetFilepath(Image $image, $sizeName) {
		if ($sizeName === ImageConfig::ORIGINAL_SIZE_NAME) {
			return;
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
