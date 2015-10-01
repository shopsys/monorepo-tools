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
	 * @param string $filename
	 * @return \SS6\ShopBundle\Component\Domain\Domain
	 */
	public function create($filename) {
		$domain = new Domain($this->domainsConfigLoader->loadDomainConfigsFromYaml($filename));

		$domainId = getenv('DOMAIN');
		if ($domainId !== false) {
			$domain->switchDomainById($domainId);
		}

		return $domain;
	}

}
