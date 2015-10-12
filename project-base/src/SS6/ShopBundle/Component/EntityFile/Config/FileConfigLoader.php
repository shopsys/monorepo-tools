<?php

namespace SS6\ShopBundle\Component\EntityFile\Config;

use SS6\ShopBundle\Component\EntityFile\Config\FileConfig;
use SS6\ShopBundle\Component\EntityFile\Config\FileEntityConfig;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Parser;

class FileConfigLoader {

	/**
	 * @var \Symfony\Component\Filesystem\Filesystem
	 */
	private $filesystem;

	/**
	 * @var \SS6\ShopBundle\Component\EntityFile\Config\FileEntityConfig[class]
	 */
	private $fileEntityConfigsByClass;

	/**
	 * @var string[entityName]
	 */
	private $entityNamesByEntityNames;

	/**
	 * @param \Symfony\Component\Filesystem\Filesystem $filesystem
	 */
	public function __construct(Filesystem $filesystem) {
		$this->filesystem = $filesystem;
	}

	/**
	 * @param string $filename
	 * @return \SS6\ShopBundle\Component\EntityFile\Config\FileConfig
	 */
	public function loadFromYaml($filename) {
		$yamlParser = new Parser();

		if (!$this->filesystem->exists($filename)) {
			throw new \Symfony\Component\Filesystem\Exception\FileNotFoundException(
				'File ' . $filename . ' does not exist'
			);
		}

		$fileConfigDefinition = new FileConfigDefinition();
		$processor = new Processor();

		$inputConfig = $yamlParser->parse(file_get_contents($filename));
		$outputConfig = $processor->processConfiguration($fileConfigDefinition, [$inputConfig]);
		$this->loadFileEntityConfigsFromArray($outputConfig);

		return new FileConfig($this->fileEntityConfigsByClass);
	}

	/**
	 * @param array $outputConfig
	 * @return \SS6\ShopBundle\Component\EntityFile\Config\FileEntityConfig[]
	 */
	private function loadFileEntityConfigsFromArray($outputConfig) {
		$this->fileEntityConfigsByClass = [];
		$this->entityNamesByEntityNames = [];

		foreach ($outputConfig as $entityConfig) {
			try {
				$imageEntityConfig = $this->processEntityConfig($entityConfig);
				$this->entityNamesByEntityNames[$imageEntityConfig->getEntityName()] = $imageEntityConfig->getEntityName();
				$this->fileEntityConfigsByClass[$imageEntityConfig->getEntityClass()] = $imageEntityConfig;
			} catch (\SS6\ShopBundle\Component\EntityFile\Config\Exception\FileConfigException $e) {
				throw new \SS6\ShopBundle\Component\EntityFile\Config\Exception\FilesConfigurationParseException(
					$entityConfig[FileConfigDefinition::CONFIG_CLASS],
					$e
				);
			}
		}
	}

	/**
	 * @param array $entityConfig
	 * @return \SS6\ShopBundle\Component\EntityFile\Config\FileEntityConfig
	 */
	private function processEntityConfig($entityConfig) {
		$entityClass = $entityConfig[FileConfigDefinition::CONFIG_CLASS];
		$entityName = $entityConfig[FileConfigDefinition::CONFIG_ENTITY_NAME];

		if (array_key_exists($entityClass, $this->fileEntityConfigsByClass)
			|| array_key_exists($entityName, $this->entityNamesByEntityNames)
		) {
			throw new \SS6\ShopBundle\Component\EntityFile\Config\Exception\DuplicateEntityNameException($entityName);
		}

		return new FileEntityConfig($entityName, $entityClass);
	}

}
