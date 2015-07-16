<?php

namespace SS6\ShopBundle\Model\Image\Processing;

use Intervention\Image\Constraint;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;
use SS6\ShopBundle\Model\Image\Config\ImageSizeConfig;
use Symfony\Component\Filesystem\Filesystem;

class ImageProcessingService {

	const EXTENSION_JPEG = 'jpeg';
	const EXTENSION_JPG = 'jpg';
	const EXTENSION_PNG = 'png';
	const EXTENSION_GIF = 'gif';

	const DOMAIN_ICON_WIDTH = 16;
	const DOMAIN_ICON_HEIGHT = 11;

	/**
	 * @var string[]
	 */
	private $supportedImageExtensions;

	/**
	 * @var \Intervention\Image\ImageManager
	 */
	private $imageManager;

	/**
	 * @var \Symfony\Component\Filesystem\Filesystem
	 */
	private $filesystem;

	/**
	 * @var string
	 */
	private $domainImagesDirectory;

	public function __construct(
		$domainImagesDirectory,
		ImageManager $imageManager,
		Filesystem $filesystem
	) {
		$this->domainImagesDirectory = $domainImagesDirectory;
		$this->imageManager = $imageManager;
		$this->filesystem = $filesystem;

		$this->supportedImageExtensions = [
			self::EXTENSION_JPEG,
			self::EXTENSION_JPG,
			self::EXTENSION_GIF,
			self::EXTENSION_PNG,
		];
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
	 * @param string $filepath
	 * @return string
	 */
	public function convertToShopFormatAndGetNewFilename($filepath) {
		$extension = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));
		$newFilepath = pathinfo($filepath, PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR . pathinfo($filepath, PATHINFO_FILENAME) . '.';

		if ($extension === self::EXTENSION_PNG) {
			$newFilepath .= self::EXTENSION_PNG;
		} elseif (in_array($extension, $this->supportedImageExtensions)) {
			$newFilepath .= self::EXTENSION_JPG;
		} else {
			throw new \SS6\ShopBundle\Model\Image\Processing\Exception\FileIsNotSupportedImageException($filepath);
		}

		$image = $this->createInterventionImage($filepath)->save($newFilepath);
		if (realpath($filepath) !== realpath($newFilepath)) {
			$this->filesystem->remove($filepath);
		}

		return $image->filename . '.' . $image->extension;
	}

	/**
	 * @param int $domainId
	 * @param string $filepath
	 */
	public function convertToDomainIconFormatAndSave($domainId, $filepath) {
		$extension = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));
		$newTemporaryFilepath = pathinfo($filepath, PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR . $domainId . '.';

		if (in_array($extension, $this->supportedImageExtensions)) {
			$newTemporaryFilepath .= self::EXTENSION_PNG;
		} else {
			throw new \SS6\ShopBundle\Model\Image\Processing\Exception\FileIsNotSupportedImageException($filepath);
		}

		$this->createInterventionImage($filepath)
			->resize(self::DOMAIN_ICON_WIDTH, self::DOMAIN_ICON_HEIGHT, function (Constraint $constraint) {
				$constraint->aspectRatio();
				$constraint->upsize();
			})
			->save($newTemporaryFilepath);

		$targetFileName = pathinfo($newTemporaryFilepath, PATHINFO_BASENAME);
		$targetFilePath = $this->domainImagesDirectory . DIRECTORY_SEPARATOR . $targetFileName;

		try {
			$this->filesystem->rename($newTemporaryFilepath, $targetFilePath, true);
		} catch (\Symfony\Component\Filesystem\Exception\IOException $ex) {
			$message = 'Failed to rename file from temporary directory to domain directory';
			throw new \SS6\ShopBundle\Model\FileUpload\Exception\MoveToFolderFailedException($message, $ex);
		}
	}

	/**
	 * @param \Intervention\Image\Image $image
	 * @param int|null $width
	 * @param int|null $height
	 * @param bool $crop
	 * @return \Intervention\Image\Image
	 */
	public function resize(Image $image, $width, $height, $crop = false) {
		if ($crop) {
			$image->fit($width, $height, function (Constraint $constraint) {
				$constraint->aspectRatio();
				$constraint->upsize();
			});
		} else {
			$image->resize($width, $height, function (Constraint $constraint) {
				$constraint->aspectRatio();
				$constraint->upsize();
			});
		}

		return $image;
	}

	/**
	 * @param \Intervention\Image\Image $image
	 * @param \SS6\ShopBundle\Model\Image\Config\ImageSizeConfig $sizeConfig
	 */
	public function resizeBySizeConfig(Image $image, ImageSizeConfig $sizeConfig) {
		$this->resize($image, $sizeConfig->getWidth(), $sizeConfig->getHeight(), $sizeConfig->getCrop());
	}

}
