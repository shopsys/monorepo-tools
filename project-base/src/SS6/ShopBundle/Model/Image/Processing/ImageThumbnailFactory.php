<?php

namespace SS6\ShopBundle\Model\Image\Processing;

use Intervention\Image\ImageManager;
use Intervention\Image\Constraint;

class ImageThumbnailFactory {

	// icon-font size 5x
	const THUMBNAIL_WIDTH = 60;
	const THUMBNAIL_HEIGHT = 70;

	/**
	 * @var string[]
	 */
	private $supportedImageExtensions;

	/**
	 * @var \Intervention\Image\ImageManager
	 */
	private $imageManager;

	/**
	 * @param \Intervention\Image\ImageManager $imageManager
	 */
	public function __construct(ImageManager $imageManager) {
		$this->imageManager = $imageManager;
		$this->supportedImageExtensions = ['jpg', 'jpeg', 'png', 'gif'];
	}

	/**
	 * @param string $filepath
	 * @return \Intervention\Image\Image
	 */
	public function getImageThumbnail($filepath) {
		$image = $this->createImage($filepath);
		$image->resize(self::THUMBNAIL_WIDTH, self::THUMBNAIL_HEIGHT, function (Constraint $constraint) {
			$constraint->aspectRatio();
		});

		return $image;
	}

	/**
	 * @param string $filepath
	 * @return \Intervention\Image\Image
	 */
	private function createImage($filepath) {
		$extension = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));
		if (!in_array($extension, $this->supportedImageExtensions)) {
			throw new \SS6\ShopBundle\Model\Image\Processing\Exception\FileIsNotSupportedImageException($filepath);
		}

		try {
			return $this->imageManager->make($filepath);
		} catch (\Intervention\Image\Exception\NotReadableException $ex) {
			throw new \SS6\ShopBundle\Model\Image\Processing\Exception\FileIsNotSupportedImageException($filepath, $ex);
		}
	}

}
