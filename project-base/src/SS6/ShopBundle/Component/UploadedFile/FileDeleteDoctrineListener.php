<?php

namespace SS6\ShopBundle\Component\UploadedFile;

use Doctrine\ORM\Event\LifecycleEventArgs;
use SS6\ShopBundle\Component\FileUpload\FileUpload;
use SS6\ShopBundle\Component\UploadedFile\Config\UploadedFileConfig;
use SS6\ShopBundle\Component\UploadedFile\File;
use SS6\ShopBundle\Component\UploadedFile\FileFacade;
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
	 * @var \SS6\ShopBundle\Component\UploadedFile\Config\UploadedFileConfig
	 */
	private $uploadedFileConfig;

	/**
	 * @var \SS6\ShopBundle\Component\FileUpload\FileUpload
	 */
	private $fileUpload;

	public function __construct(
		ContainerInterface $container,
		Filesystem $filesystem,
		UploadedFileConfig $uploadedFileConfig,
		FileUpload $fileUpload
	) {
		$this->container = $container;
		$this->filesystem = $filesystem;
		$this->uploadedFileConfig = $uploadedFileConfig;
		$this->fileUpload = $fileUpload;
	}

	/**
	 * Prevent ServiceCircularReferenceException
	 *
	 * @return \SS6\ShopBundle\Component\UploadedFile\FileFacade
	 */
	private function getFileFacade() {
		return $this->container->get(FileFacade::class);
	}

	/**
	 * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
	 */
	public function preRemove(LifecycleEventArgs $args) {
		$entity = $args->getEntity();

		if ($this->uploadedFileConfig->hasUploadedFileEntityConfig($entity)) {
			$this->getFileFacade()->getFileByEntity($entity);
		} elseif ($entity instanceof File) {
			$this->getFileFacade()->deleteFile($entity);
		}
	}

}
