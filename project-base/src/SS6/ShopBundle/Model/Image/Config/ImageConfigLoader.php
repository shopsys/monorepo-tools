<?php

namespace SS6\ShopBundle\Model\Image\Config;

use SS6\ShopBundle\Component\Condition;
use SS6\ShopBundle\Model\Image\Config\ImageConfig;
use SS6\ShopBundle\Model\Image\Config\ImageEntityConfig;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Parser;

class ImageConfigLoader {

	/**
	 * @var \Symfony\Component\Filesystem\Filesystem
	 */
	private $filesystem;

	/**
	 * @var SS6\ShopBundle\Model\Image\Config\ImageEntityConfig[]
	 */
	private $entityConfigsCache;

	/**
	 * @var array
	 */
	private $entityNamesCache;

	/**
	 * @var array
	 */
	private $filenameMethodsByType;

	/**
	 * @param \Symfony\Component\Filesystem\Filesystem $filesystem
	 */
	public function __construct(Filesystem $filesystem) {
		$this->filesystem = $filesystem;
	}

	/**
	 * @param string $filename
	 * @return \SS6\ShopBundle\Model\Image\ImageConfig
	 */
	public function loadFromYaml($filename) {
		$yamlParser = new Parser();

		if (!$this->filesystem->exists($filename)) {
			throw new \Symfony\Component\Filesystem\Exception\FileNotFoundException(
				'File ' . $filename . ' does not exist'
			);
		}

		$menuConfiguration = new ImageConfigDefinition();
		$processor = new Processor();

		$inputConfig = $yamlParser->parse(file_get_contents($filename));
		$outputConfig = $processor->processConfiguration($menuConfiguration, array($inputConfig));

		$preparedConfig = $this->loadFromArray($outputConfig);

		$imageConfig = new ImageConfig($preparedConfig);
		
		return $imageConfig;
	}

	/**
	 * @param array $outputConfig
	 * @return \SS6\ShopBundle\Model\Image\Config\ImageEntityConfig[]
	 */
	public function loadFromArray($outputConfig) {
		$this->entityConfigsCache = array();
		$this->entityNamesCache = array();

		foreach ($outputConfig as $entityConfig) {
			try {
				$this->prepareEntityToEntityConfigsCache($entityConfig);
			} catch (\SS6\ShopBundle\Model\Image\Config\Exception\ImageConfigException $e) {
				throw new \SS6\ShopBundle\Model\Image\Config\Exception\EntityParseException(
					$entityConfig[ImageConfigDefinition::CONFIG_CLASS],
					$e
				);
			}
		}

		return $this->entityConfigsCache;
	}

	/**
	 * @param array $entityConfig
	 * @throws \SS6\ShopBundle\Model\Image\Config\Exception\DuplicateEntityNameException
	 */
	private function prepareEntityToEntityConfigsCache($entityConfig) {
		$entityClass = $entityConfig[ImageConfigDefinition::CONFIG_CLASS];
		$entityName = $entityConfig[ImageConfigDefinition::CONFIG_ENTITY_NAME];
		$this->filenameMethodsByType = array();

		if (
			!array_key_exists($entityClass, $this->entityConfigsCache) &&
			!array_key_exists($entityName, $this->entityNamesCache)
		) {
			$types = $this->prepareTypes($entityConfig[ImageConfigDefinition::CONFIG_TYPES]);
			$sizes = $this->prepareSizes($entityConfig[ImageConfigDefinition::CONFIG_SIZES]);
			if (count($sizes) > 0) {
				$this->filenameMethodsByType[ImageEntityConfig::WITHOUT_NAME_KEY] =
					$entityConfig[ImageConfigDefinition::CONFIG_FILENAME_METHOD];
			}
			$imageEntityConfig = new ImageEntityConfig($entityName, $entityClass, $this->filenameMethodsByType, $types, $sizes);
			
			$this->entityNamesCache[$entityName] = $entityName;
			$this->entityConfigsCache[$entityClass] = $imageEntityConfig;
		} else {
			throw new \SS6\ShopBundle\Model\Image\Config\Exception\DuplicateEntityNameException($entityName);
		}
	}

	/**
	 * @param array $sizes
	 * @return \SS6\ShopBundle\Model\Image\Config\ImageSizeConfig[]
	 */
	private function prepareSizes($sizes) {
		$result = array();
		foreach ($sizes as $size) {
			$sizeName = $size[ImageConfigDefinition::CONFIG_SIZE_NAME];
			$key = Condition::ifNull($sizeName, ImageEntityConfig::WITHOUT_NAME_KEY);
			if (!array_key_exists($key, $result)) {
				$result[$key] = new ImageSizeConfig(
					$sizeName,
					$size[ImageConfigDefinition::CONFIG_SIZE_WIDTH],
					$size[ImageConfigDefinition::CONFIG_SIZE_HEIGHT],
					$size[ImageConfigDefinition::CONFIG_SIZE_CROP]
				);
			} else {
				throw new \SS6\ShopBundle\Model\Image\Config\Exception\DuplicateSizeNameException($sizeName);
			}
		}

		return $result;
	}

	/**
	 * @param array $types
	 * @return array
	 */
	private function prepareTypes($types) {
		$result = array();
		foreach ($types as $type) {
			$typeName = $type[ImageConfigDefinition::CONFIG_TYPE_NAME];
			if (!array_key_exists($typeName, $result)) {
				$this->filenameMethodsByType[$typeName] = $type[ImageConfigDefinition::CONFIG_FILENAME_METHOD];
				$result[$typeName] = $this->prepareSizes($type[ImageConfigDefinition::CONFIG_SIZES]);
			} else {
				throw new \SS6\ShopBundle\Model\Image\Config\Exception\DuplicateTypeNameException($typeName);
			}
		}

		return $result;
	}

}
