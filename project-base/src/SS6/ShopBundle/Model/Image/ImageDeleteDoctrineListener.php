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

	/**
	 * @var \SS6\ShopBundle\Model\Image\ImageLocator
	 */
	private $imageLocator;

	public function __construct(
		ContainerInterface $container,
		Filesystem $filesystem,
		ImageConfig $imageConfig,
		FileUpload $fileUpload,
		ImageLocator $imageLocator
	) {
		$this->container = $container;
		$this->filesystem = $filesystem;
		$this->imageConfig = $imageConfig;
		$this->fileUpload = $fileUpload;
		$this->imageLocator = $imageLocator;
	}

	/**
	 * Prevent ServiceCircularReferenceException
	 *
	 * @return \SS6\ShopBundle\Model\Image\ImageFacade
	 */
	private function getImageFacade() {
		return $this->container->get('ss6.shop.image.image_facade');
	}

	/**
	 * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
	 */
	public function preRemove(LifecycleEventArgs $args) {
		$entity = $args->getEntity();

		if ($this->imageConfig->hasImageConfig($entity)) {
			$this->deleteEntityImages($entity, $args->getEntityManager());
		} elseif ($entity instanceof Image) {
			$this->getImageFacade()->deleteImageFiles($entity);
		}
	}

	/**
	 * @param object $entity
	 * @param \Doctrine\ORM\EntityManager $em
	 * @param \SS6\ShopBundle\Model\Image\ImageFacade $imageFacade
	 */
	private function deleteEntityImages($entity, EntityManager $em) {
		$images = $this->getImageFacade()->getAllImagesByEntity($entity);
		if (count($images) > 0) {
			foreach ($images as $entity) {
				$em->remove($entity);
			}
		}
	}

}
