<?php

namespace SS6\ShopBundle\Model\Domain;

use SS6\ShopBundle\Model\Domain\Config\DomainsConfigLoader;
use Symfony\Component\HttpFoundation\RequestStack;

class DomainFactory {

	/**
	 * @var \Symfony\Component\HttpFoundation\RequestStack
	 */
	private $requestStack;

	/**
	 * @var \SS6\ShopBundle\Model\Domain\Config\DomainsConfigLoader
	 */
	private $domainsConfigLoader;

	/**
	 * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
	 * @param \SS6\ShopBundle\Model\Domain\Config\DomainsConfigLoader $domainsConfigLoader
	 */
	public function __construct(RequestStack $requestStack, DomainsConfigLoader $domainsConfigLoader) {
		$this->requestStack = $requestStack;
		$this->domainsConfigLoader = $domainsConfigLoader;
	}

	/**
	 * @param string $filename
	 * @return \SS6\ShopBundle\Model\Domain\Domain
	 */
	public function create($filename) {
		$domain = new Domain($this->domainsConfigLoader->loadDomainConfigsFromYaml($filename));

		$request = $this->requestStack->getMasterRequest();
		if ($request !== null) {
			$domain->switchDomainByRequest($this->requestStack->getMasterRequest());
		} else {
			$domainId = getenv('DOMAIN');
			if ($domainId === false) {
				throw new \SS6\ShopBundle\Model\Domain\Exception\NoDomainSelectedException(
					'Use DOMAIN environment variable to set proper domain ID'
				);
			}
			$domain->switchDomain($domainId);
		}

		return $domain;
	}

}
