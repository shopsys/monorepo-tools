<?php

namespace SS6\ShopBundle\Component\EntityFile;

use Doctrine\ORM\Event\LifecycleEventArgs;
use SS6\ShopBundle\Component\EntityFile\Config\FileConfig;
use SS6\ShopBundle\Component\EntityFile\File;
use SS6\ShopBundle\Component\EntityFile\FileFacade;
use SS6\ShopBundle\Component\EntityFile\FileLocator;
use SS6\ShopBundle\Component\FileUpload\FileUpload;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;

class FileDeleteDoctrineListener {

	/**
	 * @var \Symfony\Component\DependencyInjection\ContainerInterface
	 */
	private $container;

	/**
	 * @var \Symfony\Component\Filesystem\Filesystem
	 */
	private $filesystem;

	/**
	 * @var \SS6\ShopBundle\Component\EntityFile\Config\FileConfig
	 */
	private $fileConfig;

	/**
	 * @var \SS6\ShopBundle\Component\FileUpload\FileUpload
	 */
	private $fileUpload;

	/**
	 * @var \SS6\ShopBundle\Component\EntityFile\FileLocator
	 */
	private $fileLocator;

	public function __construct(
		ContainerInterface $container,
		Filesystem $filesystem,
		FileConfig $fileConfig,
		FileUpload $fileUpload,
		FileLocator $fileLocator
	) {
		$this->container = $container;
		$this->filesystem = $filesystem;
		$this->fileConfig = $fileConfig;
		$this->fileUpload = $fileUpload;
		$this->fileLocator = $fileLocator;
	}

	/**
	 * Prevent ServiceCircularReferenceException
	 *
	 * @return \SS6\ShopBundle\Component\EntityFile\FileFacade
	 */
	private function getFileFacade() {
		return $this->container->get(FileFacade::class);
	}

	/**
	 * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
	 */
	public function preRemove(LifecycleEventArgs $args) {
		$entity = $args->getEntity();

		if ($this->fileConfig->hasFileConfig($entity)) {
			$this->getFileFacade()->getFileByEntity($entity);
		} elseif ($entity instanceof File) {
			$this->getFileFacade()->deleteFile($entity);
		}
	}

}
