<?php

namespace SS6\ShopBundle\Component\UploadedFile;

use SS6\ShopBundle\Component\UploadedFile\Config\UploadedFileConfig;
use SS6\ShopBundle\Component\UploadedFile\UploadedFileLocator;
use Symfony\Component\Filesystem\Filesystem;

class DirectoryStructureCreator {

	/**
	 * @var \SS6\ShopBundle\Component\UploadedFile\Config\UploadedFileConfig
	 */
	private $uploadedFileConfig;

	/**
	 * @var \SS6\ShopBundle\Component\UploadedFile\UploadedFileLocator
	 */
	private $uploadedFileLocator;

	/**
	 * @var \Symfony\Component\Filesystem\Filesystem
	 */
	private $filesysytem;

	public function __construct(
		UploadedFileConfig $uploadedFileConfig,
		UploadedFileLocator $uploadedFileLocator,
		Filesystem $filesystem
	) {
		$this->uploadedFileConfig = $uploadedFileConfig;
		$this->uploadedFileLocator = $uploadedFileLocator;
		$this->filesysytem = $filesystem;
	}

	public function makeUploadedFileDirectories() {
		$uploadedFileEntityConfigs = $this->uploadedFileConfig->getAllUploadedFileEntityConfigs();
		$directories = [];
		foreach ($uploadedFileEntityConfigs as $uploadedFileEntityConfig) {
			$directories[] = $this->uploadedFileLocator->getAbsoluteFilePath($uploadedFileEntityConfig->getEntityName());
		}

		$this->filesysytem->mkdir($directories);
	}

}
