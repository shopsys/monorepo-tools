<?php

namespace SS6\ShopBundle\Component\UploadedFile;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Component\UploadedFile\File;

class UploadedFileRepository {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	public function __construct(EntityManager $em) {
		$this->em = $em;
	}

	/**
	 * @return \Doctrine\ORM\EntityRepository
	 */
	private function getFileRepository() {
		return $this->em->getRepository(File::class);
	}

	/**
	 * @param string $entityName
	 * @param int $entityId
	 * @return \SS6\ShopBundle\Component\UploadedFile\File|null
	 */
	public function findFileByEntity($entityName, $entityId) {
		$file = $this->getFileRepository()->findOneBy([
			'entityName' => $entityName,
			'entityId' => $entityId,
		]);

		return $file;
	}

	/**
	 * @param string $entityName
	 * @param int $entityId
	 * @return \SS6\ShopBundle\Component\UploadedFile\File
	 */
	public function getFileByEntity($entityName, $entityId) {
		$file = $this->findFileByEntity($entityName, $entityId);
		if ($file === null) {
			$message = 'File not found for entity "' . $entityName . '" with ID ' . $entityId;
			throw new \SS6\ShopBundle\Component\UploadedFile\Exception\FileNotFoundException($message);
		}

		return $file;
	}

	/**
	 * @param int $fileId
	 * @return \SS6\ShopBundle\Component\UploadedFile\File
	 */
	public function getById($fileId) {
		$file = $this->getFileRepository()->find($fileId);

		if ($file === null) {
			$message = 'File with ID ' . $fileId . ' does not exist.';
			throw new \SS6\ShopBundle\Component\UploadedFile\Exception\FileNotFoundException($message);
		}

		return $file;
	}

}
