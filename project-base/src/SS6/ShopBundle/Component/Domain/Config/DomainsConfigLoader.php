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
		$domainConfigsByDomainId = $processedConfig[DomainsConfigDefinition::CONFIG_DOMAINS];
		$domainUrlsConfigsByDomainId = $processedUrlsConfig[DomainsUrlsConfigDefinition::CONFIG_DOMAINS_URLS];

		if (!$this->isConfigMatchingUrlsConfig($domainConfigsByDomainId, $domainUrlsConfigsByDomainId)) {
			$message =
				'File ' . $domainsUrlsConfigFilepath . ' does not contain urls for all domains listed in ' . $domainsConfigFilepath;
			throw new \SS6\ShopBundle\Component\Domain\Config\Exception\DomainConfigsDoNotMatchException($message);
		}
		$processedConfigsWithUrlsByDomainId = $this->addUrlsToProccessedConfig($domainConfigsByDomainId, $domainUrlsConfigsByDomainId);

		$domainConfigs = $this->loadDomainConfigsFromArray($processedConfigsWithUrlsByDomainId);

		return $domainConfigs;
	}

	/**
	 * @param array $processedConfigsByDomainId
	 * @return \SS6\ShopBundle\Component\Domain\Config\DomainConfig[]
	 */
	private function loadDomainConfigsFromArray($processedConfigsByDomainId) {
		$domainConfigs = [];

		foreach ($processedConfigsByDomainId as $domainConfigArray) {
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
			$domainConfig[DomainsConfigDefinition::CONFIG_STYLES_DIRECTORY]
		);
	}

	/**
	 * @param array $domainConfigsByDomainId
	 * @param array $domainUrlsConfigsByDomainId
	 * @return array
	 */
	private function addUrlsToProccessedConfig($domainConfigsByDomainId, $domainUrlsConfigsByDomainId) {
		foreach ($domainConfigsByDomainId as $domainId => $domainConfigArray) {
			$domainConfigArray[DomainsUrlsConfigDefinition::CONFIG_URL] =
				$domainUrlsConfigsByDomainId[$domainId][DomainsUrlsConfigDefinition::CONFIG_URL];
			$domainConfigsByDomainId[$domainId] = $domainConfigArray;
		}

		return $domainConfigsByDomainId;
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
	 * @param array $domainConfigsByDomainId
	 * @param array $domainUrlsConfigsByDomainId
	 * @return bool
	 */
	private function isConfigMatchingUrlsConfig($domainConfigsByDomainId, $domainUrlsConfigsByDomainId) {
		foreach (array_keys($domainConfigsByDomainId) as $domainId) {
			if (!array_key_exists($domainId, $domainUrlsConfigsByDomainId)) {
				return false;
			}
		}

		return true;
	}

}
