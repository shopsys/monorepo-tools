<?php

namespace SS6\ShopBundle\Component\UploadedFile;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Component\Domain\Config\DomainConfig;
use SS6\ShopBundle\Component\UploadedFile\Config\UploadedFileConfig;
use SS6\ShopBundle\Component\UploadedFile\File;
use SS6\ShopBundle\Component\UploadedFile\UploadedFileLocator;
use SS6\ShopBundle\Component\UploadedFile\UploadedFileRepository;
use SS6\ShopBundle\Component\UploadedFile\UploadedFileService;
use Symfony\Component\Filesystem\Filesystem;

class FileFacade {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Component\UploadedFile\Config\UploadedFileConfig
	 */
	private $uploadedFileConfig;

	/**
	 * @var \SS6\ShopBundle\Component\UploadedFile\UploadedFileRepository
	 */
	private $uploadedFileRepository;

	/**
	 * @var \SS6\ShopBundle\Component\UploadedFile\UploadedFileService
	 */
	private $uploadedFileService;

	/**
	 * @var \Symfony\Component\Filesystem\Filesystem
	 */
	private $filesystem;

	/**
	 * @var \SS6\ShopBundle\Component\UploadedFile\UploadedFileLocator
	 */
	private $uploadedFileLocator;

	public function __construct(
		EntityManager $em,
		UploadedFileConfig $uploadedFileConfig,
		UploadedFileRepository $uploadedFileRepository,
		UploadedFileService $uploadedFileService,
		Filesystem $filesystem,
		UploadedFileLocator $uploadedFileLocator
	) {
		$this->em = $em;
		$this->uploadedFileConfig = $uploadedFileConfig;
		$this->uploadedFileRepository = $uploadedFileRepository;
		$this->uploadedFileService = $uploadedFileService;
		$this->filesystem = $filesystem;
		$this->uploadedFileLocator = $uploadedFileLocator;
	}

	/**
	 * @param object $entity
	 * @param array|null $temporaryFilenames
	 */
	public function uploadFile($entity, $temporaryFilenames) {
		if ($temporaryFilenames !== null && count($temporaryFilenames) > 0) {
			$entitiesForFlush = [];
			$fileEntityConfig = $this->uploadedFileConfig->getUploadedFileEntityConfig($entity);
			$entityId = $this->getEntityId($entity);
			$oldFile = $this->uploadedFileRepository->findFileByEntity($fileEntityConfig->getEntityName(), $entityId);

			if ($oldFile !== null) {
				$this->em->remove($oldFile);
				$entitiesForFlush[] = $oldFile;
			}

			$newFile = $this->uploadedFileService->createFile(
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
	 */
	public function deleteFileByEntity($entity) {
		$file = $this->getFileByEntity($entity);
		$this->em->remove($file);
		$this->em->flush();
	}

	/**
	 * @param \SS6\ShopBundle\Component\UploadedFile\File $file
	 */
	public function deleteFile(File $file) {
		$entityName = $file->getEntityName();
		$filepath = $this->uploadedFileLocator->getAbsoluteFileFilepath($file);
		$this->filesystem->remove($filepath);
	}

	/**
	 * @param object $entity
	 * @return \SS6\ShopBundle\Component\UploadedFile\File
	 */
	public function getFileByEntity($entity) {
		return $this->uploadedFileRepository->getFileByEntity(
			$this->uploadedFileConfig->getEntityName($entity),
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
		throw new \SS6\ShopBundle\Component\UploadedFile\Exception\EntityIdentifierException($message);
	}

	/**
	 * @param \SS6\ShopBundle\Component\UploadedFile\File|Object $fileOrEntity
	 * @return \SS6\ShopBundle\Component\UploadedFile\File
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
	 * @return \SS6\ShopBundle\Component\UploadedFile\File
	 */
	public function getById($fileId) {
		return $this->uploadedFileRepository->getById($fileId);
	}

	/**
	 * @param Object $entity
	 * @return bool
	 */
	public function hasFile($entity) {
		try {
			$file = $this->getFileByEntity($entity);
		} catch (\SS6\ShopBundle\Component\UploadedFile\Exception\FileNotFoundException $e) {
			return false;
		}

		return $this->uploadedFileLocator->fileExists($file);
	}

	/**
	 * @param \SS6\ShopBundle\Component\UploadedFile\File $file
	 * @return string
	 */
	public function getAbsoluteFileFilepath(File $file) {
		return $this->uploadedFileLocator->getAbsoluteFileFilepath($file);
	}

	/**
	 * @param \SS6\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
	 * @param \SS6\ShopBundle\Component\UploadedFile\File $file
	 * @return string
	 */
	public function getFileUrl(DomainConfig $domainConfig, File $file) {
		return $this->uploadedFileLocator->getFileUrl($domainConfig, $file);
	}

}
