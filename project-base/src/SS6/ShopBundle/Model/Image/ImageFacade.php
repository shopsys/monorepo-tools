<?php

namespace SS6\ShopBundle\Model\Image;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Image\Config\ImageConfig;
use SS6\ShopBundle\Model\Image\Image;
use SS6\ShopBundle\Model\Image\ImageRepository;

class ImageFacade {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\Image\Config\ImageConfig
	 */
	private $imageConfig;

	/**
	 * @var \SS6\ShopBundle\Model\Image\ImageRepository
	 */
	private $imageRepository;

	public function __construct(EntityManager $em, ImageConfig $imageConfig, ImageRepository $imageRepository) {
		$this->em = $em;
		$this->imageConfig = $imageConfig;
		$this->imageRepository = $imageRepository;
	}

		/**
	 * @param object $entity
	 * @param string|null $imageForUpload
	 * @param string|null $type
	 */
	public function uploadImage($entity, $imageForUpload, $type) {
		if ($imageForUpload !== null) {
			$image = $this->getImageByEntityOrCreate($entity, $type, $imageForUpload);
			$this->em->flush($image);
		}
	}

	/**
	 * @param object $entity
	 * @param string|null $type
	 * @return \SS6\ShopBundle\Model\Image\Image
	 */
	public function getImageByEntity($entity, $type) {
		return $this->imageRepository->getImageByEntity(
			$this->imageConfig->getEntityName($entity),
			$this->getEntityId($entity),
			$type
		);
	}

	/**
	 * @param object $entity
	 * @param string|null $type
	 * @param string $imageForUpload
	 * @return \SS6\ShopBundle\Model\Image\Image
	 */
	private function getImageByEntityOrCreate($entity, $type, $imageForUpload) {
		try {
			$image = $this->getImageByEntity($entity, $type);
			$image->setImageForUpload($imageForUpload);
		} catch (\SS6\ShopBundle\Model\Image\Exception\ImageNotFoundException $e) {
			$entityName = $this->imageConfig->getEntityName($entity);
			$entityId = $this->getEntityId($entity);
			$image = new Image($entityName, $entityId, $type, $imageForUpload);
			$this->em->persist($image);
		}

		return $image;
	}

	/**
	 * @param object $entity
	 * @return int
	 */
	private function getEntityId($entity) {
		$entityMetadata = $this->em->getClassMetadata(get_class($entity));
		$identifier = $entityMetadata->getIdentifierValues($entity);
		if (count($identifier) === 1) {
			return array_pop($identifier);
		}

		$message = 'Entity "' . get_class($entity) . '" has not set primary key or primary key is compound."';
		throw new \SS6\ShopBundle\Model\Image\Exception\EntityIdentifierException($message);
	}
}
