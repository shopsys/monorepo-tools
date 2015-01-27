<?php

namespace SS6\ShopBundle\Model\Image;

use SS6\ShopBundle\Model\Image\Config\ImageConfig;
use SS6\ShopBundle\Model\Image\ImageLocator;
use Symfony\Component\Filesystem\Filesystem;

class DirectoryStructureCreator {

	/**
	 * @var \SS6\ShopBundle\Model\ImageConfig\ImageConfig
	 */
	private $imageConfig;

	/**
	 * @var \SS6\ShopBundle\Model\Image\ImageLocator
	 */
	private $imageLocator;

	/**
	 * @var \Symfony\Component\Filesystem\Filesystem
	 */
	private $filesysytem;

	/**
	 * @var string
	 */
	private $imageDir;

	/**
	 * @param string $imageDir
	 * @param \SS6\ShopBundle\Model\Image\Config\ImageConfig $imageConfig
	 * @param \SS6\ShopBundle\Model\Image\ImageLocator $imageLocator
	 * @param \Symfony\Component\Filesystem\Filesystem $filesystem
	 */
	public function __construct($imageDir, ImageConfig $imageConfig, ImageLocator $imageLocator, Filesystem $filesystem) {
		$this->imageDir = $imageDir;
		$this->imageConfig = $imageConfig;
		$this->imageLocator = $imageLocator;
		$this->filesysytem = $filesystem;
	}

	public function makeImageDirectories() {
		$imageEntityConfigs = $this->imageConfig->getAllImageEntityConfigsByClass();
		$directories = [];
		foreach ($imageEntityConfigs as $imageEntityConfig) {
			$sizeConfigs = $imageEntityConfig->getSizeConfigs();
			$sizesDirectories = $this->getTargetDirectoriesFromSizeConfigs(
				$imageEntityConfig->getEntityName(),
				null,
				$sizeConfigs
			);
			$directories = array_merge($directories, $sizesDirectories);

			foreach ($imageEntityConfig->getTypes() as $type) {
				$typeSizes = $imageEntityConfig->getSizeConfigsByType($type);
				$typeSizesDirectories = $this->getTargetDirectoriesFromSizeConfigs(
					$imageEntityConfig->getEntityName(),
					$type,
					$typeSizes
				);
				$directories = array_merge($directories, $typeSizesDirectories);
			}
		}

		$this->filesysytem->mkdir($directories);
	}

	/**
	 * @param string $entityName
	 * @param string|null $type
	 * @param \SS6\ShopBundle\Model\Image\Config\ImageSizeConfig[] $sizeConfigs
	 * @return type
	 */
	private function getTargetDirectoriesFromSizeConfigs($entityName, $type, array $sizeConfigs) {
		$directories = [];
		foreach ($sizeConfigs as $sizeConfig) {
			$relativePath = $this->imageLocator->getRelativeImagePath($entityName, $type, $sizeConfig->getName());
			$directories[] = $this->imageDir . DIRECTORY_SEPARATOR . $relativePath;
		}

		return $directories;
	}

}
