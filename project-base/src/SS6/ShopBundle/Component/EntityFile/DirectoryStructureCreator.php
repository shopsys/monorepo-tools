<?php

namespace SS6\ShopBundle\Component\EntityFile;

use SS6\ShopBundle\Component\EntityFile\Config\FileConfig;
use SS6\ShopBundle\Component\EntityFile\FileLocator;
use Symfony\Component\Filesystem\Filesystem;

class DirectoryStructureCreator {

	/**
	 * @var \SS6\ShopBundle\Component\EntityFile\Config\FileConfig
	 */
	private $fileConfig;

	/**
	 * @var \SS6\ShopBundle\Component\EntityFile\FileLocator
	 */
	private $fileLocator;

	/**
	 * @var \Symfony\Component\Filesystem\Filesystem
	 */
	private $filesysytem;

	public function __construct(
		FileConfig $fileConfig,
		FileLocator $fileLocator,
		Filesystem $filesystem
	) {
		$this->fileConfig = $fileConfig;
		$this->fileLocator = $fileLocator;
		$this->filesysytem = $filesystem;
	}

	public function makeFileDirectories() {
		$fileEntityConfigs = $this->fileConfig->getAllFileEntityConfigs();
		$directories = [];
		foreach ($fileEntityConfigs as $fileEntityConfig) {
			$directories[] = $this->fileLocator->getAbsoluteFilePath($fileEntityConfig->getEntityName());
		}

		$this->filesysytem->mkdir($directories);
	}

}
