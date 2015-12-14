<?php

namespace SS6\ShopBundle\Component\Domain\Config;

use SS6\ShopBundle\Component\Domain\Config\DomainsUrlsConfigDefinition;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Parser;

class DomainsConfigLoader {

	/**
	 * @var \Symfony\Component\Filesystem\Filesystem
	 */
	private $filesystem;
	/**
	 * @param \Symfony\Component\Filesystem\Filesystem $filesystem
	 */
	public function __construct(Filesystem $filesystem) {
		$this->filesystem = $filesystem;
	}

	/**
	 * @param string $domainsConfigFilepath
	 * @param string $domainsUrlsConfigFilepath
	 * @return \SS6\ShopBundle\Component\Domain\Config\DomainConfig[]
	 */
	public function loadDomainConfigsFromYaml($domainsConfigFilepath, $domainsUrlsConfigFilepath) {
		$processedConfig = $this->getProcessedConfig($domainsConfigFilepath, new DomainsConfigDefinition());
		$processedUrlsConfig = $this->getProcessedConfig($domainsUrlsConfigFilepath, new DomainsUrlsConfigDefinition());

		if (!$this->isConfigMatchingUrlsConfig($processedConfig, $processedUrlsConfig)) {
			$message =
				'File ' . $domainsUrlsConfigFilepath . ' does not contain urls for all domains listed in ' . $domainsConfigFilepath;
			throw new \SS6\ShopBundle\Component\Domain\Config\Exception\DomainConfigsDoNotMatchException($message);
		}
		$proccessedConfigWithUrls = $this->addUrlsToProccessedConfig($processedConfig, $processedUrlsConfig);

		$domainConfigs = $this->loadDomainConfigsFromArray($proccessedConfigWithUrls);

		return $domainConfigs;
	}

	/**
	 * @param array $processedConfig
	 * @return \SS6\ShopBundle\Component\Domain\Config\DomainConfig[]
	 */
	private function loadDomainConfigsFromArray($processedConfig) {
		$domainConfigs = [];

		foreach ($processedConfig[DomainsConfigDefinition::CONFIG_DOMAINS] as $domainConfigArray) {
			$domainConfigs[] = $this->processDomainConfigArray($domainConfigArray);
		}

		return $domainConfigs;
	}

	/**
	 * @param array $domainConfig
	 * @return \SS6\ShopBundle\Component\Domain\Config\DomainConfig
	 */
	private function processDomainConfigArray(array $domainConfig) {
		return new DomainConfig(
			$domainConfig[DomainsConfigDefinition::CONFIG_ID],
			$domainConfig[DomainsUrlsConfigDefinition::CONFIG_URL],
			$domainConfig[DomainsConfigDefinition::CONFIG_NAME],
			$domainConfig[DomainsConfigDefinition::CONFIG_LOCALE],
			$domainConfig[DomainsConfigDefinition::CONFIG_TEMPLATES_DIRECTORY],
			$domainConfig[DomainsConfigDefinition::CONFIG_STYLES_DIRECTORY]
		);
	}

	/**
	 * @param array $processedConfig
	 * @param array $processedUrlsConfig
	 * @return array
	 */
	private function addUrlsToProccessedConfig($processedConfig, $processedUrlsConfig) {
		foreach ($processedConfig[DomainsConfigDefinition::CONFIG_DOMAINS] as $domainId => $domainConfigArray) {
			$domainConfigArray[DomainsUrlsConfigDefinition::CONFIG_URL] =
				$processedUrlsConfig[DomainsUrlsConfigDefinition::CONFIG_DOMAINS_URLS][$domainId][DomainsUrlsConfigDefinition::CONFIG_URL];
			$processedConfig[DomainsConfigDefinition::CONFIG_DOMAINS][$domainId] = $domainConfigArray;
		}

		return $processedConfig;
	}

	/**
	 * @param string $filepath
	 * @param \Symfony\Component\Config\Definition\ConfigurationInterface $configDefinition
	 * @return array
	 */
	private function getProcessedConfig($filepath, ConfigurationInterface $configDefinition) {
		$yamlParser = new Parser();
		$processor = new Processor();

		if (!$this->filesystem->exists($filepath)) {
			throw new \Symfony\Component\Filesystem\Exception\FileNotFoundException(
				'File ' . $filepath . ' does not exist'
			);
		}

		$parsedConfig = $yamlParser->parse(file_get_contents($filepath));

		return $processor->processConfiguration($configDefinition, [$parsedConfig]);
	}

	/**
	 * @param array $processedConfig
	 * @param array $processedUrlsConfig
	 * @return bool
	 */
	private function isConfigMatchingUrlsConfig($processedConfig, $processedUrlsConfig) {
		foreach (array_keys($processedConfig[DomainsConfigDefinition::CONFIG_DOMAINS]) as $domainId) {
			if (!array_key_exists($domainId, $processedUrlsConfig[DomainsUrlsConfigDefinition::CONFIG_DOMAINS_URLS])) {
				return false;
			}
		}

		return true;
	}

}
