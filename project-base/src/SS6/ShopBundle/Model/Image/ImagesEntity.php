<?php

namespace SS6\ShopBundle\Model\Image;

use SS6\ShopBundle\Model\Image\Config\ImageConfig;

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
	 * @param string $imageDir
	 * @param \SS6\ShopBundle\Model\Image\Config\ImageConfig $imageConfig
	 */
	public function __construct($imageDir, ImageConfig $imageConfig) {
		$this->imageDir = $imageDir;
		$this->imageConfig = $imageConfig;
	}

	/**
	 * @param Object $entity
	 * @param string|null $type
	 * @param string|null $sizeName
	 * @return string
	 * @throws \SS6\ShopBundle\Model\Image\Exception\EntityFilenameMethodNotFoundException
	 */
	public function getRelativeImageFilepath($entity, $type, $sizeName) {
		$imageEntityConfig = $this->imageConfig->getImageEntityConfig($entity);
		$filenameMethodName = $imageEntityConfig->getFilenameMethodByType($type);

		if (!method_exists($entity, $filenameMethodName)) {
			throw new \SS6\ShopBundle\Model\Image\Exception\EntityFilenameMethodNotFoundException($entity, $filenameMethodName);
		}

		$filename = $entity->$filenameMethodName();
		$path = $this->getRelativeImagePath($imageEntityConfig->getEntityName(), $type, $sizeName);

		return $path . $filename;
	}

	/**
	 * @param Object $entity
	 * @param string|null $type
	 * @param string|null $sizeName
	 * @return bool
	 */
	public function imageExists($entity, $type, $sizeName) {
		$relativeImageFilepath = $this->getRelativeImageFilepath($entity, $type, $sizeName);
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
