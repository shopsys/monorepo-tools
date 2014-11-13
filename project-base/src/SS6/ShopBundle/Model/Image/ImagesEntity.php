<?php

namespace SS6\ShopBundle\Model\Image;

use SS6\ShopBundle\Model\Image\Config\ImageConfig;
use SS6\ShopBundle\Model\Image\ImageFacade;

class ImagesEntity {

	/**
	 * @var string
	 */
	private $imageDir;

	/**
	 * @var \SS6\ShopBundle\Model\Image\Config\ImageConfig
	 */
	private $imageConfig;

	/**
	 * @var \SS6\ShopBundle\Model\Image\ImageFacade
	 */
	private $imageFacade;

	public function __construct($imageDir, ImageConfig $imageConfig, ImageFacade $imageFacade) {
		$this->imageDir = $imageDir;
		$this->imageConfig = $imageConfig;
		$this->imageFacade = $imageFacade;
	}

	/**
	 * @param Object $entity
	 * @param string|null $type
	 * @param string|null $sizeName
	 * @return string
	 */
	public function getRelativeImageFilepath($entity, $type, $sizeName) {
		$image = $this->imageFacade->getImageByEntity($entity, $type);
		$path = $this->getRelativeImagePath($image->getEntityName(), $type, $sizeName);

		return $path . $image->getFilename();
	}

	/**
	 * @param Object $entity
	 * @param string|null $type
	 * @param string|null $sizeName
	 * @return bool
	 */
	public function imageExists($entity, $type, $sizeName) {
		try {
			$relativeImageFilepath = $this->getRelativeImageFilepath($entity, $type, $sizeName);
		} catch (\SS6\ShopBundle\Model\Image\Exception\ImageNotFoundException $e) {
			return false;
		}
		$imageFilepath = $this->imageDir . DIRECTORY_SEPARATOR . $relativeImageFilepath;

		return is_file($imageFilepath) && is_readable($imageFilepath);
	}

	/**
	 * @param string $entityName
	 * @param string|null $type
	 * @param string|null $sizeName
	 * @return string
	 */
	public function getRelativeImagePath($entityName, $type, $sizeName) {
		$pathParts = array($entityName);

		if ($type !== null) {
			$pathParts[] = $type;
		}
		if ($sizeName !== null) {
			$pathParts[] = $sizeName;
		}

		return implode(DIRECTORY_SEPARATOR, $pathParts) . DIRECTORY_SEPARATOR;
	}
}
