<?php

namespace SS6\ShopBundle\Component\EntityFile;

use SS6\ShopBundle\Component\EntityFile\Config\FileConfig;
use SS6\ShopBundle\Component\EntityFile\File;

class FileLocator {

	/**
	 * @var string
	 */
	private $fileDir;

	/**
	 * @var \SS6\ShopBundle\Component\EntityFile\Config\FileConfig
	 */
	private $fileConfig;

	public function __construct($fileDir, FileConfig $fileConfig) {
		$this->fileDir = $fileDir;
		$this->fileConfig = $fileConfig;
	}

	/**
	 * @param \SS6\ShopBundle\Component\EntityFile\File $file
	 * @return string
	 */
	public function getRelativeFileFilepath(File $file) {
		return $this->getRelativeFilePath($file->getEntityName()) . DIRECTORY_SEPARATOR . $file->getFilename();
	}

	/**
	 * @param \SS6\ShopBundle\Component\EntityFile\File $file
	 * @return string
	 */
	public function getAbsoluteFileFilepath(File $file) {
		return $this->getAbsoluteFilePath($file->getEntityName()) . DIRECTORY_SEPARATOR . $file->getFilename();
	}

	/**
	 * @param \SS6\ShopBundle\Component\EntityFile\File $file
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
