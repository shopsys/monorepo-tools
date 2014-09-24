<?php

namespace SS6\ShopBundle\Model\Image;

use SS6\ShopBundle\Model\Image\Config\ImageConfig;
use SS6\ShopBundle\Model\Image\ImagesEntity;
use Symfony\Component\Filesystem\Filesystem;

class DirectoryStructureCreator {

	/**
	 * @var \SS6\ShopBundle\Model\ImageConfig\ImageConfig
	 */
	private $imageConfig;

	/**
	 * @var \SS6\ShopBundle\Model\Image\ImagesEntity
	 */
	private $imagesEntity;

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
	 * @param \SS6\ShopBundle\Model\Image\ImagesEntity $imagesEntity
	 * @param \Symfony\Component\Filesystem\Filesystem $filesystem
	 */
	public function __construct($imageDir, ImageConfig $imageConfig, ImagesEntity $imagesEntity, Filesystem $filesystem) {
		$this->imageDir = $imageDir;
		$this->imageConfig = $imageConfig;
		$this->imagesEntity = $imagesEntity;
		$this->filesysytem = $filesystem;
	}

	public function makeImageDirectories() {
		$imageEntityConfigs = $this->imageConfig->getAllImageEntityConfigsByClass();
		$directories = [];
		foreach ($imageEntityConfigs as $imageEntityConfig) {
			$sizes = $imageEntityConfig->getSizes();
			$sizesDirectories = $this->getTargetDirectoriesFromSizes($imageEntityConfig->getEntityName(), null, $sizes);
			$directories = array_merge($directories, $sizesDirectories);

			foreach ($imageEntityConfig->getTypes() as $type) {
				$typeSizes = $imageEntityConfig->getTypeSizes($type);
				$typeSizesDirectories = $this->getTargetDirectoriesFromSizes($imageEntityConfig->getEntityName(), $type, $typeSizes);
				$directories = array_merge($directories, $typeSizesDirectories);
			}
		}

		$this->filesysytem->mkdir($directories);
	}

	/**
	 * @param string $entityName
	 * @param string|null $type
	 * @param \SS6\ShopBundle\Model\Image\Config\ImageSizeConfig $sizes
	 * @return type
	 */
	private function getTargetDirectoriesFromSizes($entityName, $type, array $sizes) {
		$directories = [];
		foreach ($sizes as $size) {
			$relativePath = $this->imagesEntity->getRelativeImagePath($entityName, $type, $size->getName());
			$directories[] = $this->imageDir . DIRECTORY_SEPARATOR . $relativePath;
		}

		return $directories;
	}

}
