<?php

namespace SS6\ShopBundle\Component\Domain;

use SS6\ShopBundle\Component\Domain\Config\DomainsConfigLoader;

class DomainFactory {

	/**
	 * @var \SS6\ShopBundle\Component\Domain\Config\DomainsConfigLoader
	 */
	private $domainsConfigLoader;

	/**
	 * @param \SS6\ShopBundle\Component\Domain\Config\DomainsConfigLoader $domainsConfigLoader
	 */
	public function __construct(DomainsConfigLoader $domainsConfigLoader) {
		$this->domainsConfigLoader = $domainsConfigLoader;
	}

	/**
	 * @param string $domainsConfigFilepath
	 * @param string $domainsUrlsConfigFilepath
	 * @return \SS6\ShopBundle\Component\Domain\Domain
	 */
	public function create($domainsConfigFilepath, $domainsUrlsConfigFilepath) {
		$domainConfigs = $this->domainsConfigLoader->loadDomainConfigsFromYaml($domainsConfigFilepath, $domainsUrlsConfigFilepath);
		$domain = new Domain($domainConfigs);

		$domainId = getenv('DOMAIN');
		if ($domainId !== false) {
			$domain->switchDomainById($domainId);
		}

		return $domain;
	}

}
