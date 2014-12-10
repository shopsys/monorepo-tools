<?php

namespace SS6\ShopBundle\Model\Image;

use SS6\ShopBundle\Model\Image\Config\ImageConfig;
use SS6\ShopBundle\Model\Image\Config\ImageEntityConfig;
use SS6\ShopBundle\Model\Image\Image;

class ImageService {

	/**
	 * @param SS6\ShopBundle\Model\Image\Config\ImageEntityConfig $imageEntityConfig
	 * @param int $entityId
	 * @param array $temporaryFilenames
	 * @param string|null $type
	 * @return
	 */
	public function getUploadedImages(ImageEntityConfig $imageEntityConfig, $entityId, array $temporaryFilenames, $type) {
		if (!$imageEntityConfig->isMultiple($type)) {
			$message = 'Entity ' . $imageEntityConfig->getEntityClass()
				. ' in not allowed to have multiple images for type ' . ($type ?: 'NULL');
			throw new \SS6\ShopBundle\Model\Image\Exception\EntityMultipleImageException($message);
		}

		$images = array();
		foreach ($temporaryFilenames as $temporaryFilename) {
			$images[] = new Image($imageEntityConfig->getEntityName(), $entityId, $type, $temporaryFilename);
		}

		return $images;
	}

	/**
	 * @param SS6\ShopBundle\Model\Image\Config\ImageEntityConfig $imageEntityConfig
	 * @param int $entityId
	 * @param string $temporaryFilename
	 * @param string|null $type
	 * @param \SS6\ShopBundle\Model\Image\Image $image
	 * @return \SS6\ShopBundle\Model\Image\Image
	 */
	public function editImageOrCreateNew(
		ImageEntityConfig $imageEntityConfig,
		$entityId,
		$temporaryFilename,
		$type,
		Image $image = null
	) {
		if ($image === null) {
			$image = new Image($imageEntityConfig->getEntityName(), $entityId, $type, $temporaryFilename);
		} else {
			$image->setTemporaryFilename($temporaryFilename);
		}

		return $image;
	}

	/**
	 * @param string $entityName
	 * @param int $entityId
	 * @param \SS6\ShopBundle\Model\Image\Image[] $images
	 */
	public function deleteImages($entityName, $entityId, array $images) {
		foreach ($images as $image) {
			$this->deleteImage($entityName, $entityId, $image);
		}
	}

	/**
	 * @param string $entityName
	 * @param int $entityId
	 * @param \SS6\ShopBundle\Model\Image\Image[] $images
	 */
	private function deleteImage($entityName, $entityId, Image $image) {
		if ($image->getEntityName() !== $entityName
			|| $image->getEntityId() !== $entityId
		) {
			throw new \SS6\ShopBundle\Model\Image\Exception\ImageNotFoundException(
				sprintf(
					'Entity %s with ID %s does not own image with ID',
					$entityName,
					$entityId,
					$image->getId()
				)
			);
		}
	}

}
