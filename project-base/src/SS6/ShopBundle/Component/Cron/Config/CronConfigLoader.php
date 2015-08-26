<?php

namespace SS6\ShopBundle\Component\Cron\Config;

use SS6\ShopBundle\Component\Cron\Config\CronConfigDefinition;
use SS6\ShopBundle\Component\Cron\Config\CronServiceConfig;
use SS6\ShopBundle\Component\Cron\CronTimeResolver;
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
	 * @var \SS6\ShopBundle\Component\Cron\CronTimeResolver
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
	 * @return \SS6\ShopBundle\Component\Cron\Config\CronServiceConfig[]
	 */
	public function loadCronServiceConfigsFromYaml($filename) {
		if (!$this->filesystem->exists($filename)) {
			throw new \Symfony\Component\Filesystem\Exception\FileNotFoundException(
				'File ' . $filename . ' does not exist'
			);
		}

		$cronConfigDefinition = new CronConfigDefinition();

		$parsedConfig = $this->yamlParser->parse(file_get_contents($filename));
		$processedConfig = $this->processor->processConfiguration($cronConfigDefinition, [$parsedConfig]);

		return $this->loadCronServiceConfigsFromArray($processedConfig);
	}

	/**
	 * @param array $processedConfig
	 * @return \SS6\ShopBundle\Component\Cron\Config\CronServiceConfig[]
	 */
	private function loadCronServiceConfigsFromArray($processedConfig) {
		$cronServiceConfigs = [];

		foreach ($processedConfig as $cronServiceConfigArray) {
			$cronServiceConfigs[] = $this->processCronServiceConfigArray($cronServiceConfigArray);
		}

		return $cronServiceConfigs;
	}

	/**
	 * @param array $cronServiceConfigArray
	 * @return \SS6\ShopBundle\Component\Cron\Config\CronServiceConfig
	 */
	private function processCronServiceConfigArray(array $cronServiceConfigArray) {
		$serviceId = $cronServiceConfigArray[CronConfigDefinition::CONFIG_SERVICE];
		$timeMinutes = $cronServiceConfigArray[CronConfigDefinition::CONFIG_TIME][CronConfigDefinition::CONFIG_TIME_MINUTES];
		$timeHours = $cronServiceConfigArray[CronConfigDefinition::CONFIG_TIME][CronConfigDefinition::CONFIG_TIME_HOURS];

		if (!$this->container->has($serviceId)) {
			throw new \SS6\ShopBundle\Component\Cron\Config\Exception\CronServiceNotFoundException($serviceId);
		}
		$this->cronTimeResolver->validateTimeString($timeMinutes, 55, 5);
		$this->cronTimeResolver->validateTimeString($timeHours, 23, 1);

		return new CronServiceConfig($this->container->get($serviceId), $serviceId, $timeMinutes, $timeHours);
	}

}
