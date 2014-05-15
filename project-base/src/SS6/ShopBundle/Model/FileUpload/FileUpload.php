<?php

namespace SS6\ShopBundle\Model\FileUpload;

use SS6\ShopBundle\Model\String\TransformString;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUpload {

	const CACHE_DIRECTORY = 'fileUploads';
	const UPLOAD_FILE_DIRECTORY = 'files';
	const UPLOAD_IMAGE_DIRECTORY = 'images';

	/**
	 * @var string
	 */
	private $cacheDir;

	/**
	 * @var string
	 */
	private $fileDir;

	/**
	 * @var string
	 */
	private $imageDir;

	/**
	 * @var \SS6\ShopBundle\Model\FileUpload\FileNamingConvention
	 */
	private $fileNamingConvention;

	/**
	 * @var \Symfony\Component\Filesystem\Filesystem
	 */
	private $filesystem;

	/**
	 * @param string $cacheDir
	 * @param string $fileDir
	 * @param string $imageDir
	 * @param \SS6\ShopBundle\Model\FileUpload\FileNamingConvention $fileNamingConvention
	 * @param \Symfony\Component\Filesystem\Filesystem $filesystem
	 */
	public function __construct($cacheDir, $fileDir, $imageDir, FileNamingConvention $fileNamingConvention,
			Filesystem $filesystem) {
		$this->cacheDir = $cacheDir;
		$this->fileDir = $fileDir;
		$this->imageDir = $imageDir;
		$this->fileNamingConvention = $fileNamingConvention;
		$this->filesystem = $filesystem;
	}

	/**
	 * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
	 * @throws \SS6\ShopBundle\Model\FileUpload\Exception\UploadFailedException
	 */
	public function upload(UploadedFile $file) {
		if ($file->getError()) {
			throw new \SS6\ShopBundle\Model\FileUpload\Exception\UploadFailedException($file->getErrorMessage(), $file->getError());
		}

		$cacheFilename = $this->getCacheFilename($file->getClientOriginalName());
		$file->move($this->getCacheDirectory(), $cacheFilename);

		return $cacheFilename;
	}

	/**
	 * @param string $filename
	 * @return boolean
	 */
	public function tryDeleteCachedFile($filename) {
		$filepath = $this->getCacheFilepath($filename);
		try {
			$this->filesystem->remove($filepath);
		} catch (\Symfony\Component\Filesystem\Exception\IOException $ex) {
			return false;
		}
		return true;
	}

	/**
	 * @param string $filename
	 * @return string
	 */
	private function getCacheFilename($filename) {
		return TransformString::safeFilename(uniqid() . '__' . $filename);
	}

	/**
	 * @param string $cacheFilename
	 * @return string
	 */
	private function getCacheFilepath($cacheFilename) {
		return $this->getCacheDirectory() . DIRECTORY_SEPARATOR . TransformString::safeFilename($cacheFilename);
	}

	/**
	 * @return string
	 */
	public function getCacheDirectory() {
		return $this->cacheDir . DIRECTORY_SEPARATOR . self::CACHE_DIRECTORY;
	}

	/**
	 *
	 * @param string $isImage
	 * @param string $category
	 * @param string|null $type
	 * @return string
	 */
	public function getUploadDirectory($isImage, $category, $type) {
		if ($isImage) {
			return $this->imageDir .
				DIRECTORY_SEPARATOR . $category .
				($type !== null ? DIRECTORY_SEPARATOR . $type : '');
		} else {
			return $this->fileDir .
				DIRECTORY_SEPARATOR . $category .
				($type !== null ? DIRECTORY_SEPARATOR . $type : '');
		}
		
	}

	/**
	 * @param strinf $filename
	 * @param bool $isImage
	 * @param string $category
	 * @param string|null $type
	 * @return string
	 */
	private function getTargetFilepath($filename, $isImage, $category, $type) {
		return $this->getUploadDirectory($isImage, $category, $type) . DIRECTORY_SEPARATOR . $filename;
	}

	/**
	 * @param string $cachedFilename
	 * @return string
	 */
	public function getOriginFilenameByCached($cachedFilename) {
		$matches = array();
		if ($cachedFilename && preg_match('/^.+?__(.+)$/', $cachedFilename, $matches)) {
			return $matches[1];
		}
		return '';
	}

	/**
	 * @param \SS6\ShopBundle\Model\FileUpload\EntityFileUploadInterface $entity
	 */
	public function preFlushEntity(EntityFileUploadInterface $entity) {
		$filesForUpload = $entity->getCachedFilesForUpload();
		foreach ($filesForUpload as $key => $fileForUpload) {
			/* @var $fileForUpload FileForUpload */
			$originFilename = $this->getOriginFilenameByCached($fileForUpload->getCacheFilename());
			$entity->setFileAsUploaded($key, $originFilename);
		}
	}

	/**
	 * @param \SS6\ShopBundle\Model\FileUpload\EntityFileUploadInterface $entity
	 * @throws \SS6\ShopBundle\Model\FileUpload\Exception\MoveToEntityFailedException
	 */
	public function postFlushEntity(EntityFileUploadInterface $entity) {
		$filesForUpload = $entity->getCachedFilesForUpload();
		foreach ($filesForUpload as $key => $fileForUpload) {
			/* @var $fileForUpload FileForUpload */
			$sourceFilepath = $this->getCacheFilepath($fileForUpload->getCacheFilename());
			$originFilename = $this->fileNamingConvention->getFilenameByNamingConvention(
				$fileForUpload->getNameConventionType(),
				$fileForUpload->getCacheFilename(),
				$entity->getId()
			);
			$targetFilename = $this->getTargetFilepath(
				$originFilename,
				$fileForUpload->isImage(),
				$fileForUpload->getCategory(),
				$fileForUpload->getType()
			);

			try {
				$this->filesystem->rename($sourceFilepath, $targetFilename, true);
			} catch (\Symfony\Component\Filesystem\Exception\IOException $ex) {
				$message = 'Failed rename file from cache to entity';
				throw new \SS6\ShopBundle\Model\FileUpload\Exception\MoveToEntityFailedException($message, $ex);
			}
		}
	}
	
}
