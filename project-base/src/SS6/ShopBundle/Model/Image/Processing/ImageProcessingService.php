<?php

namespace SS6\ShopBundle\Model\Image\Processing;

use Intervention\Image\ImageManager;
use SS6\ShopBundle\Model\FileUpload\FileUpload;

class ImageProcessingService {

	const EXTENSION_JPG = 'jpg';
	const EXTENSION_PNG = 'png';
	const EXTENSION_JPEG = 'jpeg';
	const EXTENSION_GIF = 'gif';

	/**
	 * @var string[]
	 */
	private $supportedImageExtensions;

	/**
	 * @var \Intervention\Image\ImageManager
	 */
	private $imageManager;

	/**
	 * @var \SS6\ShopBundle\Model\FileUpload\FileUpload
	 */
	private $fileUpload;

	/**
	 * @param \Intervention\Image\ImageManager $imageManager
	 */
	public function __construct(ImageManager $imageManager, FileUpload $fileUpload) {
		$this->imageManager = $imageManager;
		$this->supportedImageExtensions = [
			self::EXTENSION_JPG,
			self::EXTENSION_GIF,
			self::EXTENSION_JPEG,
			self::EXTENSION_PNG,
		];
		$this->fileUpload = $fileUpload;
	}

	/**
	 * @param string $filepath
	 * @return \Intervention\Image\Image
	 */
	public function createInterventionImage($filepath) {
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

	/**
	 * @param string $filename
	 * @return \Intervention\Image\Image
	 */
	public function convertImage($filename) {
		$extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
		$filepath = $this->fileUpload->getTemporaryFilePath($filename);
		$path = pathinfo($filepath, PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR . pathinfo($filepath, PATHINFO_FILENAME) . '.';
		try {
			if ($extension === self::EXTENSION_PNG) {
				$path .= self::EXTENSION_PNG;
				return $this->imageManager->make($filepath)->save($path);
			} elseif (in_array($extension, $this->supportedImageExtensions)) {
				$path .= self::EXTENSION_JPG;
				return $this->imageManager->make($filepath)->save($path);
			} else {
				throw new \SS6\ShopBundle\Model\Image\Processing\Exception\FileIsNotSupportedImageException($filepath);
			}
		} catch (\Intervention\Image\Exception\NotReadableException $ex) {
			throw new \SS6\ShopBundle\Model\Image\Processing\Exception\FileIsNotSupportedImageException($filepath, $ex);
		}
	}

	/**
	 * @param string $filename
	 * @return string
	 */
	public function convertImageAndGetConvertedFilename($filename) {
		$interventionImage = $this->convertImage($filename);
		return $interventionImage->filename . '.' . $interventionImage->extension;
	}
}
