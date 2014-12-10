<?php

namespace SS6\ShopBundle\Model\Image;

use SS6\ShopBundle\Model\Image\ImageFacade;

class ImageLocator {

	/**
	 * @var string
	 */
	private $imageDir;

	/**
	 * @var \SS6\ShopBundle\Model\Image\ImageFacade
	 */
	private $imageFacade;

	public function __construct($imageDir, ImageFacade $imageFacade) {
		$this->imageDir = $imageDir;
		$this->imageFacade = $imageFacade;
	}

	/**
	 * @param Object $entity
	 * @param string|null $type
	 * @param string|null $sizeName
	 * @return string
	 */
	public function getRelativeImageFilepathByEntityAndType($entity, $type, $sizeName) {
		$image = $this->imageFacade->getImageByEntity($entity, $type);
		$path = $this->getRelativeImagePath($image->getEntityName(), $type, $sizeName);

		return $path . $image->getFilename();
	}

	/**
	 * @param \SS6\ShopBundle\Model\Image\Image $image
	 * @param string|null $sizeName
	 * @return string
	 */
	public function getRelativeImageFilepath(Image $image, $sizeName) {
		$path = $this->getRelativeImagePath($image->getEntityName(), $image->getType(), $sizeName);

		return $path . $image->getFilename();
	}

	/**
	 * @param Object $entity
	 * @param string|null $type
	 * @param string|null $sizeName
	 * @return array
	 */
	public function getRelativeImagesFilepathByEntityAndType($entity, $type, $sizeName) {
		$filepaths = array();

		$images = $this->imageFacade->getImagesByEntity($entity, $type);
		foreach ($images as $image) {
			$filepaths[] = $this->getRelativeImagePath($image->getEntityName(), $type, $sizeName) . $image->getFilename();
		}

		return $filepaths;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Image\Image $image
	 * @param string|null $sizeName
	 * @return bool
	 */
	public function imageExists(Image $image, $sizeName) {
		$relativeImageFilepath = $this->getRelativeImageFilepath($image, $sizeName);
		$imageFilepath = $this->imageDir . DIRECTORY_SEPARATOR . $relativeImageFilepath;

		return is_file($imageFilepath) && is_readable($imageFilepath);
	}

	/**
	 * @param Object $entity
	 * @param string|null $type
	 * @param string|null $sizeName
	 * @return bool
	 */
	public function imageExistsByEntityAndType($entity, $type, $sizeName) {
		try {
			$image = $this->imageFacade->getImageByEntity($entity, $type);
		} catch (\SS6\ShopBundle\Model\Image\Exception\ImageNotFoundException $e) {
			return false;
		}

		return $this->imageExists($image, $sizeName);
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
