<?php

namespace SS6\ShopBundle\Model\Image;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Image\Config\ImageConfig;
use SS6\ShopBundle\Model\Image\ImageRepository;
use SS6\ShopBundle\Model\Image\ImageService;

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

	/**
	 * @var \SS6\ShopBundle\Model\Image\ImageService
	 */
	private $imageService;

	public function __construct(
		EntityManager $em,
		ImageConfig $imageConfig,
		ImageRepository $imageRepository,
		ImageService $imageService
	) {
		$this->em = $em;
		$this->imageConfig = $imageConfig;
		$this->imageRepository = $imageRepository;
		$this->imageService = $imageService;
	}

	/**
	 * @param object $entity
	 * @param array|null $temporaryFilenames
	 * @param string|null $type
	 */
	public function uploadImage($entity, $temporaryFilenames, $type) {
		if ($temporaryFilenames !== null && count($temporaryFilenames) > 0) {
			$imageEntityConfig = $this->imageConfig->getImageEntityConfig($entity);
			$entityId = $this->getEntityId($entity);
			$oldImage = $this->imageRepository->findImageByEntity($imageEntityConfig->getEntityName(), $entityId, $type);

			$image = $this->imageService->editImageOrCreateNew(
				$imageEntityConfig,
				$entityId,
				array_pop($temporaryFilenames),
				$type,
				$oldImage
			);
			$this->em->persist($image);
			$this->em->flush($image);
		}
	}

	/**
	 * @param object $entity
	 * @param array|null $temporaryFilenames
	 * @param string|null $type
	 */
	public function uploadImages($entity, $temporaryFilenames, $type) {
		if ($temporaryFilenames !== null && count($temporaryFilenames) > 0) {
			$imageEntityConfig = $this->imageConfig->getImageEntityConfig($entity);
			$entityId = $this->getEntityId($entity);

			$images = $this->imageService->getUploadedImages($imageEntityConfig, $entityId, $temporaryFilenames, $type);
			foreach ($images as $image) {
				$this->em->persist($image);
			}
			$this->em->flush();
		}
	}

	/**
	 * @param object $entity
	 * @param \SS6\ShopBundle\Model\Image\Image[] $images
	 */
	public function deleteImages($entity, array $images) {
		$entityName = $this->imageConfig->getEntityName($entity);
		$entityId = $this->getEntityId($entity);

		$this->imageService->deleteImages($entityName, $entityId, $images);

		foreach ($images as $image) {
			$this->em->remove($image);
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
	 * @return \SS6\ShopBundle\Model\Image\Image[]
	 */
	public function getImagesByEntity($entity, $type) {
		return $this->imageRepository->getImagesByEntity(
			$this->imageConfig->getEntityName($entity),
			$this->getEntityId($entity),
			$type
		);
	}

	/**
	 * @param object $entity
	 * @return boolean
	 */
	public function hasImages($entity) {
		return $this->imageConfig->hasImageConfig($entity);
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
