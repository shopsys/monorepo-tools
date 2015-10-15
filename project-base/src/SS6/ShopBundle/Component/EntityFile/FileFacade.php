<?php

namespace SS6\ShopBundle\Component\EntityFile;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Component\Domain\Config\DomainConfig;
use SS6\ShopBundle\Component\EntityFile\Config\FileConfig;
use SS6\ShopBundle\Component\EntityFile\File;
use SS6\ShopBundle\Component\EntityFile\FileLocator;
use SS6\ShopBundle\Component\EntityFile\FileRepository;
use SS6\ShopBundle\Component\EntityFile\FileService;
use SS6\ShopBundle\Component\FileUpload\FileUpload;
use Symfony\Component\Filesystem\Filesystem;

class FileFacade {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Component\EntityFile\Config\FileConfig
	 */
	private $fileConfig;

	/**
	 * @var \SS6\ShopBundle\Component\EntityFile\FileRepository
	 */
	private $fileRepository;

	/**
	 * @var \SS6\ShopBundle\Component\EntityFile\FileService
	 */
	private $fileService;

	/**
	 * @var \Symfony\Component\Filesystem\Filesystem
	 */
	private $filesystem;

	/**
	 * @var \SS6\ShopBundle\Component\FileUpload\FileUpload
	 */
	private $fileUpload;

	/**
	 * @var \SS6\ShopBundle\Component\EntityFile\FileLocator
	 */
	private $fileLocator;

	public function __construct(
		EntityManager $em,
		FileConfig $fileConfig,
		FileRepository $fileRepository,
		FileService $fileService,
		Filesystem $filesystem,
		FileUpload $fileUpload,
		FileLocator $fileLocator
	) {
		$this->em = $em;
		$this->fileConfig = $fileConfig;
		$this->fileRepository = $fileRepository;
		$this->fileService = $fileService;
		$this->filesystem = $filesystem;
		$this->fileUpload = $fileUpload;
		$this->fileLocator = $fileLocator;
	}

	/**
	 * @param object $entity
	 * @param array|null $temporaryFilenames
	 */
	public function uploadFile($entity, $temporaryFilenames) {
		if ($temporaryFilenames !== null && count($temporaryFilenames) > 0) {
			$entitiesForFlush = [];
			$fileEntityConfig = $this->fileConfig->getFileEntityConfig($entity);
			$entityId = $this->getEntityId($entity);
			$oldFile = $this->fileRepository->findFileByEntity($fileEntityConfig->getEntityName(), $entityId);

			if ($oldFile !== null) {
				$this->em->remove($oldFile);
				$entitiesForFlush[] = $oldFile;
			}

			$newFile = $this->fileService->createFile(
				$fileEntityConfig,
				$entityId,
				array_pop($temporaryFilenames)
			);
			$this->em->persist($newFile);
			$entitiesForFlush[] = $newFile;

			$this->em->flush($entitiesForFlush);
		}
	}

	/**
	 * @param object $entity
	 * @return \SS6\ShopBundle\Component\EntityFile\File
	 */
	public function getFileByEntity($entity) {
		return $this->fileRepository->getFileByEntity(
			$this->fileConfig->getEntityName($entity),
			$this->getEntityId($entity)
		);
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
		throw new \SS6\ShopBundle\Component\EntityFile\Exception\EntityIdentifierException($message);
	}

	/**
	 * @param \SS6\ShopBundle\Component\EntityFile\File|Object $fileOrEntity
	 * @return \SS6\ShopBundle\Component\EntityFile\File
	 */
	public function getFileByObject($fileOrEntity) {
		if ($fileOrEntity instanceof File) {
			return $fileOrEntity;
		} else {
			return $this->getFileByEntity($fileOrEntity);
		}
	}

	/**
	 * @param int $fileId
	 * @return \SS6\ShopBundle\Component\EntityFile\File
	 */
	public function getById($fileId) {
		return $this->fileRepository->getById($fileId);
	}

	/**
	 * @param Object $entity
	 * @return bool
	 */
	public function hasFile($entity) {
		try {
			$file = $this->getFileByEntity($entity);
		} catch (\SS6\ShopBundle\Component\EntityFile\Exception\FileNotFoundException $e) {
			return false;
		}

		return $this->fileLocator->fileExists($file);
	}

	/**
	 * @param \SS6\ShopBundle\Component\EntityFile\File $file
	 * @return string
	 */
	public function getAbsoluteFileFilepath(File $file) {
		return $this->fileLocator->getAbsoluteFileFilepath($file);
	}

	/**
	 * @param \SS6\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
	 * @param \SS6\ShopBundle\Component\EntityFile\File $file
	 * @return string
	 */
	public function getFileUrl(DomainConfig $domainConfig, File $file) {
		return $this->fileLocator->getFileUrl($domainConfig, $file);
	}

}
