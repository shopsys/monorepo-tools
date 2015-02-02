<?php

namespace SS6\ShopBundle\Model\Image;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\FileUpload\FileUpload;
use SS6\ShopBundle\Model\Image\Config\ImageConfig;
use SS6\ShopBundle\Model\Image\Image;
use SS6\ShopBundle\Model\Image\ImageLocator;
use SS6\ShopBundle\Model\Image\ImageRepository;
use SS6\ShopBundle\Model\Image\ImageService;
use Symfony\Component\Filesystem\Filesystem;

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

	/**
	 * @var \Symfony\Component\Filesystem\Filesystem
	 */
	private $filesystem;

	/**
	 * @var \SS6\ShopBundle\Model\FileUpload\FileUpload
	 */
	private $fileUpload;

	/**
	 * @var \SS6\ShopBundle\Model\Image\ImageLocator
	 */
	private $imageLocator;

	public function __construct(
		EntityManager $em,
		ImageConfig $imageConfig,
		ImageRepository $imageRepository,
		ImageService $imageService,
		Filesystem $filesystem,
		FileUpload $fileUpload,
		ImageLocator $imageLocator
	) {
		$this->em = $em;
		$this->imageConfig = $imageConfig;
		$this->imageRepository = $imageRepository;
		$this->imageService = $imageService;
		$this->filesystem = $filesystem;
		$this->fileUpload = $fileUpload;
		$this->imageLocator = $imageLocator;
	}

	/**
	 * @param object $entity
	 * @param array|null $temporaryFilenames
	 * @param string|null $type
	 */
	public function uploadImage($entity, $temporaryFilenames, $type) {
		if ($temporaryFilenames !== null && count($temporaryFilenames) > 0) {
			$entitiesForFlush = [];
			$imageEntityConfig = $this->imageConfig->getImageEntityConfig($entity);
			$entityId = $this->getEntityId($entity);
			$oldImage = $this->imageRepository->findImageByEntity($imageEntityConfig->getEntityName(), $entityId, $type);

			if ($oldImage !== null) {
				$this->em->remove($oldImage);
				$entitiesForFlush[] = $oldImage;
			}

			$newImage = $this->imageService->createImage(
				$imageEntityConfig,
				$entityId,
				array_pop($temporaryFilenames),
				$type
			);
			$this->em->persist($newImage);
			$entitiesForFlush[] = $newImage;

			$this->em->flush($entitiesForFlush);
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

		// files will be deleted in doctrine listener
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
	 * @return \SS6\ShopBundle\Model\Image\Image[]
	 */
	public function getAllImagesByEntity($entity) {
		return $this->imageRepository->getAllImagesByEntity(
			$this->imageConfig->getEntityName($entity),
			$this->getEntityId($entity)
		);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Image\Image $image
	 */
	public function deleteImageFiles(Image $image) {
		$entityName = $image->getEntityName();
		$imageConfig = $this->imageConfig->getEntityConfigByEntityName($entityName);
		foreach ($imageConfig->getSizeConfigs() as $sizeConfig) {
			$filepath = $this->imageLocator->getAbsoluteImageFilepath($image, $sizeConfig->getName());
			$this->filesystem->remove($filepath);
		}
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
