<?php

namespace SS6\ShopBundle\Component\UploadedFile;

use SS6\ShopBundle\Component\UploadedFile\Config\UploadedFileConfig;
use SS6\ShopBundle\Component\UploadedFile\FileLocator;
use Symfony\Component\Filesystem\Filesystem;

class DirectoryStructureCreator {

	/**
	 * @var \SS6\ShopBundle\Component\UploadedFile\Config\UploadedFileConfig
	 */
	private $uploadedFileConfig;

	/**
	 * @var \SS6\ShopBundle\Component\UploadedFile\FileLocator
	 */
	private $fileLocator;

	/**
	 * @var \Symfony\Component\Filesystem\Filesystem
	 */
	private $filesysytem;

	public function __construct(
		UploadedFileConfig $uploadedFileConfig,
		FileLocator $fileLocator,
		Filesystem $filesystem
	) {
		$this->uploadedFileConfig = $uploadedFileConfig;
		$this->fileLocator = $fileLocator;
		$this->filesysytem = $filesystem;
	}

	public function makeFileDirectories() {
		$uploadedFileEntityConfigs = $this->uploadedFileConfig->getAllUploadedFileEntityConfigs();
		$directories = [];
		foreach ($uploadedFileEntityConfigs as $uploadedFileEntityConfig) {
			$directories[] = $this->fileLocator->getAbsoluteFilePath($uploadedFileEntityConfig->getEntityName());
		}

		$this->filesysytem->mkdir($directories);
	}

}
