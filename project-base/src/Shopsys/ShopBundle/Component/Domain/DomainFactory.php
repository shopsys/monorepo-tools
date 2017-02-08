<?php

namespace SS6\ShopBundle\Component\Domain;

use SS6\ShopBundle\Component\Domain\Config\DomainsConfigLoader;
use SS6\ShopBundle\Component\Setting\Setting;

class DomainFactory {

	/**
	 * @var \SS6\ShopBundle\Component\Domain\Config\DomainsConfigLoader
	 */
	private $domainsConfigLoader;

	/**
	 * @var \SS6\ShopBundle\Component\Setting\Setting
	 */
	private $setting;

	/**
	 * @param \SS6\ShopBundle\Component\Domain\Config\DomainsConfigLoader $domainsConfigLoader
	 * @param \SS6\ShopBundle\Component\Setting\Setting
	 */
	public function __construct(DomainsConfigLoader $domainsConfigLoader, Setting $setting) {
		$this->domainsConfigLoader = $domainsConfigLoader;
		$this->setting = $setting;
	}

	/**
	 * @param string $domainsConfigFilepath
	 * @param string $domainsUrlsConfigFilepath
	 * @return \SS6\ShopBundle\Component\Domain\Domain
	 */
	public function create($domainsConfigFilepath, $domainsUrlsConfigFilepath) {
		$domainConfigs = $this->domainsConfigLoader->loadDomainConfigsFromYaml($domainsConfigFilepath, $domainsUrlsConfigFilepath);
		$domain = new Domain($domainConfigs, $this->setting);

		$domainId = getenv('DOMAIN');
		if ($domainId !== false) {
			$domain->switchDomainById($domainId);
		}

		return $domain;
	}

}
