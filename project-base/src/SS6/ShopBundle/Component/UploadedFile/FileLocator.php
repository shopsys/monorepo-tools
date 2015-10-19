<?php

namespace SS6\ShopBundle\Component\UploadedFile;

use SS6\ShopBundle\Component\Domain\Config\DomainConfig;
use SS6\ShopBundle\Component\UploadedFile\Config\FileConfig;
use SS6\ShopBundle\Component\UploadedFile\File;

class FileLocator {

	/**
	 * @var string
	 */
	private $fileDir;

	/**
	 * @var string
	 */
	private $fileUrlPrefix;

	/**
	 * @var \SS6\ShopBundle\Component\UploadedFile\Config\FileConfig
	 */
	private $fileConfig;

	/**
	 * @param string $fileDir
	 * @param string $fileUrlPrefix
	 * @param \SS6\ShopBundle\Component\UploadedFile\Config\FileConfig $fileConfig
	 */
	public function __construct($fileDir, $fileUrlPrefix, FileConfig $fileConfig) {
		$this->fileDir = $fileDir;
		$this->fileUrlPrefix = $fileUrlPrefix;
		$this->fileConfig = $fileConfig;
	}

	/**
	 * @param \SS6\ShopBundle\Component\UploadedFile\File $file
	 * @return string
	 */
	public function getRelativeFileFilepath(File $file) {
		return $this->getRelativeFilePath($file->getEntityName()) . DIRECTORY_SEPARATOR . $file->getFilename();
	}

	/**
	 * @param \SS6\ShopBundle\Component\UploadedFile\File $file
	 * @return string
	 */
	public function getAbsoluteFileFilepath(File $file) {
		return $this->getAbsoluteFilePath($file->getEntityName()) . DIRECTORY_SEPARATOR . $file->getFilename();
	}

	/**
	 * @param \SS6\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
	 * @param \SS6\ShopBundle\Component\UploadedFile\File $file
	 * @return string
	 */
	public function getFileUrl(DomainConfig $domainConfig, File $file) {
		if ($this->fileExists($file)) {
			return $domainConfig->getUrl()
			. $this->fileUrlPrefix
			. str_replace(DIRECTORY_SEPARATOR, '/', $this->getRelativeFileFilepath($file));
		}

		throw new \SS6\ShopBundle\Component\UploadedFile\Exception\FileNotFoundException();
	}

	/**
	 * @param \SS6\ShopBundle\Component\UploadedFile\File $file
	 * @return bool
	 */
	public function fileExists(File $file) {
		$fileFilepath = $this->getAbsoluteFileFilepath($file);

		return is_file($fileFilepath) && is_readable($fileFilepath);
	}

	/**
	 * @param string $entityName
	 * @return string
	 */
	private function getRelativeFilePath($entityName) {
		return $entityName;
	}

	/**
	 * @param string $entityName
	 * @return string
	 */
	public function getAbsoluteFilePath($entityName) {
		return $this->fileDir . DIRECTORY_SEPARATOR . $this->getRelativeFilePath($entityName);
	}

}
