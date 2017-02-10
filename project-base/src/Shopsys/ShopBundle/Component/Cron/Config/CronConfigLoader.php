<?php

namespace Shopsys\ShopBundle\Component\Cron\Config;

use Shopsys\ShopBundle\Component\Cron\Config\CronConfigDefinition;
use Shopsys\ShopBundle\Component\Cron\Config\CronModuleConfig;
use Shopsys\ShopBundle\Component\Cron\CronTimeResolver;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Parser;

class CronConfigLoader {

	/**
	 * @var \Symfony\Component\DependencyInjection\ContainerInterface
	 */
	private $container;

	/**
	 * @var \Symfony\Component\Config\Definition\Processor
	 */
	private $processor;

	/**
	 * @var \Symfony\Component\Yaml\Parser
	 */
	private $yamlParser;

	/**
	 * @var \Symfony\Component\Filesystem\Filesystem
	 */
	private $filesystem;

	/**
	 * @var \Shopsys\ShopBundle\Component\Cron\CronTimeResolver
	 */
	private $cronTimeResolver;

	public function __construct(
		ContainerInterface $container,
		Filesystem $filesystem,
		Parser $yamlParser,
		Processor $processor,
		CronTimeResolver $cronTimeResolver
	) {
		$this->container = $container;
		$this->filesystem = $filesystem;
		$this->yamlParser = $yamlParser;
		$this->processor = $processor;
		$this->cronTimeResolver = $cronTimeResolver;
	}

	/**
	 * @param string $filename
	 * @return \Shopsys\ShopBundle\Component\Cron\Config\CronModuleConfig[]
	 */
	public function loadCronModuleConfigsFromYaml($filename) {
		if (!$this->filesystem->exists($filename)) {
			throw new \Symfony\Component\Filesystem\Exception\FileNotFoundException(
				'File ' . $filename . ' does not exist'
			);
		}

		$cronConfigDefinition = new CronConfigDefinition();

		$parsedConfig = $this->yamlParser->parse(file_get_contents($filename));
		$processedConfig = $this->processor->processConfiguration($cronConfigDefinition, [$parsedConfig]);

		return $this->loadCronModuleConfigsFromArray($processedConfig);
	}

	/**
	 * @param array $processedConfig
	 * @return \Shopsys\ShopBundle\Component\Cron\Config\CronModuleConfig[]
	 */
	private function loadCronModuleConfigsFromArray($processedConfig) {
		$cronModuleConfigs = [];

		foreach ($processedConfig as $cronModuleConfigArray) {
			$cronModuleConfigs[] = $this->processCronModuleConfigArray($cronModuleConfigArray);
		}

		return $cronModuleConfigs;
	}

	/**
	 * @param array $cronModuleConfigArray
	 * @return \Shopsys\ShopBundle\Component\Cron\Config\CronModuleConfig
	 */
	private function processCronModuleConfigArray(array $cronModuleConfigArray) {
		$moduleId = $cronModuleConfigArray[CronConfigDefinition::CONFIG_SERVICE];
		$timeHours = $cronModuleConfigArray[CronConfigDefinition::CONFIG_TIME][CronConfigDefinition::CONFIG_TIME_HOURS];
		$timeMinutes = $cronModuleConfigArray[CronConfigDefinition::CONFIG_TIME][CronConfigDefinition::CONFIG_TIME_MINUTES];

		if (!$this->container->has($moduleId)) {
			throw new \Shopsys\ShopBundle\Component\Cron\Config\Exception\CronModuleNotFoundException($moduleId);
		}
		$this->cronTimeResolver->validateTimeString($timeHours, 23, 1);
		$this->cronTimeResolver->validateTimeString($timeMinutes, 55, 5);

		return new CronModuleConfig($this->container->get($moduleId), $moduleId, $timeHours, $timeMinutes);
	}

}
