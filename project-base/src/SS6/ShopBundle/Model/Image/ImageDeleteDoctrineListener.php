<?php

namespace SS6\ShopBundle\Model\Image;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use SS6\ShopBundle\Model\FileUpload\FileUpload;
use SS6\ShopBundle\Model\Image\Config\ImageConfig;
use SS6\ShopBundle\Model\Image\Image;
use SS6\ShopBundle\Model\Image\ImageLocator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;

class ImageDeleteDoctrineListener {

	/**
	 * @var \Symfony\Component\DependencyInjection\ContainerInterface
	 */
	private $container;

	/**
	 * @var \Symfony\Component\Filesystem\Filesystem
	 */
	private $filesystem;

	/**
	 * @var \SS6\ShopBundle\Model\Image\Config\ImageConfig
	 */
	private $imageConfig;

	/**
	 * @var \SS6\ShopBundle\Model\FileUpload\FileUpload
	 */
	private $fileUpload;

	public function __construct(
		ContainerInterface $container,
		Filesystem $filesystem,
		ImageConfig $imageConfig,
		FileUpload $fileUpload
	) {
		$this->container = $container;
		$this->filesystem = $filesystem;
		$this->imageConfig = $imageConfig;
		$this->fileUpload = $fileUpload;
	}

	/**
	 * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
	 */
	public function preRemove(LifecycleEventArgs $args) {
		$imageFacade = $this->container->get('ss6.shop.image.image_facade');
		/* @var $imageFacade \SS6\ShopBundle\Model\Image\ImageFacade */

		$entity = $args->getEntity();

		if ($imageFacade->hasImages($entity)) {
			$em = $args->getEntityManager();
			$this->deleteEntityImages($entity, $em, $imageFacade);
		}

		if ($entity instanceof Image) {
			$this->deleteImageFiles($entity);
		}
	}

	/**
	 * @param object $entity
	 * @param \Doctrine\ORM\EntityManager $em
	 * @param \SS6\ShopBundle\Model\Image\ImageFacade $imageFacade
	 */
	private function deleteEntityImages($entity, EntityManager $em, ImageFacade $imageFacade) {
		$images = $imageFacade->getImagesByEntity($entity, null);
		if (count($images) > 0) {
			foreach ($images as $entity) {
				$em->remove($entity);
			}
		}
	}

	/**
	 * @param \SS6\ShopBundle\Model\Image\Image $image
	 */
	private function deleteImageFiles(Image $image) {
		$imageLocator = $this->container->get('ss6.shop.image.image_locator');
		/* @var $imageLocator \SS6\ShopBundle\Model\Image\ImageLocator */
		$entityName = $image->getEntityName();
		$imageConfig = $this->imageConfig->getEntityConfigByEntityName($entityName);
		foreach ($imageConfig->getSizes() as $size) {
			$filePath = $imageLocator->getAbsoluteImageFilePath($image, $size->getName());
			$this->filesystem->remove($filePath);
		}
	}

}
