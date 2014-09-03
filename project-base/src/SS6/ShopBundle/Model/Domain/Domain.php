<?php

namespace SS6\ShopBundle\Model\Domain;

use Symfony\Component\HttpFoundation\Request;

class Domain {

	/**
	 * @var int
	 */
	private $id;

	/**
	 * @var \SS6\ShopBundle\Model\Domain\Config\DomainConfig[]
	 */
	private $domainConfigs;

	/**
	 * @param \SS6\ShopBundle\Model\Domain\Config\DomainConfig[] $domainConfigs
	 */
	public function __construct(array $domainConfigs) {
		$this->domainConfigs = $domainConfigs;
	}

	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param int $domainId
	 */
	public function switchDomain($domainId) {
		if ($domainId === null || !array_key_exists($domainId, $this->domainConfigs)) {
			throw new \SS6\ShopBundle\Model\Domain\Exception\InvalidDomainIdException();
		}

		$this->id = $domainId;
	}

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function switchDomainByRequest(Request $request) {
		$host = $request->getHost();

		foreach ($this->domainConfigs as $domainConfig) {
			if ($domainConfig->getDomain() === $host) {
				$this->id = $domainConfig->getId();
				return;
			}
		}

		throw new \SS6\ShopBundle\Model\Domain\Exception\UnableToResolveDomainException();
	}

	/**
	 * @return string
	 */
	public function getTemplatesDirectory() {
		return $this->getCurrentConfig()->getTemplatesDirectory();
	}

	/**
	 * @return \SS6\ShopBundle\Model\Domain\Config\DomainConfig
	 */
	private function getCurrentConfig() {
		if ($this->id === null) {
			throw new \SS6\ShopBundle\Model\Domain\Exception\NoDomainSelectedException();
		}

		return $this->domainConfigs[$this->id];
	}

}
