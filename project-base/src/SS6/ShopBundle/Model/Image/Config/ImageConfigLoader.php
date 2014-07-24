<?php

namespace SS6\ShopBundle\Model\Image\Config;

use SS6\ShopBundle\Model\Image\Config\ImageConfig;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Parser;

class ImageConfigLoader {

	/**
	 * @var \Symfony\Component\Filesystem\Filesystem
	 */
	private $filesystem;

	/**
	 * @var array
	 */
	private $entityConfigsCache;

	/**
	 * @var array
	 */
	private $entityNamesCache;

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

		$preparedConfig = $this->prepareConfig($outputConfig);

		$imageConfig = new ImageConfig($preparedConfig);
		
		return $imageConfig;
	}

	/**
	 * @param array $outputConfig
	 * @return array
	 */
	private function prepareConfig($outputConfig) {
		$this->entityConfigsCache = array();
		$this->entityNamesCache = array();

		foreach ($outputConfig as $entityConfig) {
			try {
				$this->prepareEntityToEntityConfigsCache($entityConfig);
			} catch (\SS6\ShopBundle\Model\Image\Config\Exception\ImageConfigException $e) {
				throw new \SS6\ShopBundle\Model\Image\Config\Exception\EntityParseException($entityConfig['class'], $e);
			}
		}

		return $this->entityConfigsCache;
	}

	/**
	 * @param type $entityConfig
	 * @return type
	 */
	private function prepareEntityToEntityConfigsCache($entityConfig) {
		if (
			!array_key_exists($entityConfig['class'], $this->entityConfigsCache) &&
			!array_key_exists($entityConfig['name'], $this->entityNamesCache)
		) {
			$this->entityNamesCache[$entityConfig['name']] = $entityConfig['name'];
			$this->entityConfigsCache[$entityConfig['class']] = array(
				'name' => $entityConfig['name'],
				'types' => $this->prepareTypes($entityConfig['types']),
				'sizes' => $this->prepareSizes($entityConfig['sizes']),
			);
		} else {
			throw new \SS6\ShopBundle\Model\Image\Config\Exception\DuplicateEntityNameException($entityConfig['name']);
		}
	}

	/**
	 * @param array $sizes
	 * @return \SS6\ShopBundle\Model\Image\Config\ImageTypeInfo[]
	 */
	private function prepareSizes($sizes) {
		$result = array();
		foreach ($sizes as $size) {
			$key = $size['name'] !== null ? $size['name'] : ImageConfig::WITHOUT_NAME_KEY;
			if (!array_key_exists($key, $result)) {
				$result[$key] = new ImageTypeInfo(
					$size['name'],
					$size['width'],
					$size['height'],
					$size['crop']
				);
			} else {
				throw new \SS6\ShopBundle\Model\Image\Config\Exception\DuplicateSizeNameException($size['name']);
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
			$key = $type['name'] !== null ? $type['name'] : ImageConfig::WITHOUT_NAME_KEY;
			if (!array_key_exists($key, $result)) {
				$result[$key] = array(
					'name' => $type['name'],
					'sizes' => $this->prepareSizes($type['sizes']),
				);
			} else {
				throw new \SS6\ShopBundle\Model\Image\Config\Exception\DuplicateTypeNameException($type['name']);
			}
		}

		return $result;
	}

}
