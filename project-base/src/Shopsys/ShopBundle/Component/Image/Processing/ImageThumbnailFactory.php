<?php

namespace SS6\ShopBundle\Component\Image\Processing;

use SS6\ShopBundle\Component\Image\Processing\ImageProcessingService;

class ImageThumbnailFactory {

	const THUMBNAIL_WIDTH = 140;
	const THUMBNAIL_HEIGHT = 200;

	/**
	 * @var \SS6\ShopBundle\Component\Image\Processing\ImageProcessingService
	 */
	private $imageProcessingService;

	/**
	 * @param \SS6\ShopBundle\Component\Image\Processing\ImageProcessingService $imageProcessingService
	 */
	public function __construct(ImageProcessingService $imageProcessingService) {
		$this->imageProcessingService = $imageProcessingService;
	}

	/**
	 * @param string $filepath
	 * @return \Intervention\Image\Image
	 */
	public function getImageThumbnail($filepath) {
		$image = $this->imageProcessingService->createInterventionImage($filepath);
		$this->imageProcessingService->resize($image, self::THUMBNAIL_WIDTH, self::THUMBNAIL_HEIGHT);

		return $image;
	}

}
