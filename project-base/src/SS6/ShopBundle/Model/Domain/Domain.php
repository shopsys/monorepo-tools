<?php

namespace SS6\ShopBundle\Model\Domain;

use SS6\ShopBundle\Model\Domain\Config\DomainConfig;
use Symfony\Component\HttpFoundation\Request;

class Domain {

	/**
	 * @var \SS6\ShopBundle\Model\Domain\Config\DomainConfig|null
	 */
	private $currentDomainConfig;

	/**
	 * @var \SS6\ShopBundle\Model\Domain\Config\DomainConfig[]
	 */
	private $domainConfigs;

	/**
	 * @var \SS6\ShopBundle\Model\Domain\Config\DomainConfig[]
	 */
	private $queueDomainConfig = [];

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
		return $this->getCurrentConfig()->getId();
	}

	/**
	 * @return string
	 */
	public function getTemplatesDirectory() {
		return $this->getCurrentConfig()->getTemplatesDirectory();
	}

	/**
	 * @param int $domainId
	 */
	public function switchDomainById($domainId) {
		foreach ($this->domainConfigs as $domainConfig) {
			if ($domainId === $domainConfig->getId()) {
				$this->switchDomain($domainConfig);
				return;
			}
		}
	
		throw new \SS6\ShopBundle\Model\Domain\Exception\InvalidDomainIdException();
	}

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function switchDomainByRequest(Request $request) {
		$host = $request->getHost();

		foreach ($this->domainConfigs as $domainConfig) {
			if ($domainConfig->getDomain() === $host) {
				$this->switchDomain($domainConfig);
				return;
			}
		}

		throw new \SS6\ShopBundle\Model\Domain\Exception\UnableToResolveDomainException();
	}

	public function revertDomain() {
		if (count($this->queueDomainConfig) === 0) {
			throw new \SS6\ShopBundle\Model\Domain\Exception\DomainQueueEmptyException();
		}

		$this->currentDomainConfig = array_pop($this->queueDomainConfig);
	}

	/**
	 * @return \SS6\ShopBundle\Model\Domain\Config\DomainConfig
	 */
	private function getCurrentConfig() {
		if ($this->currentDomainConfig === null) {
			throw new \SS6\ShopBundle\Model\Domain\Exception\NoDomainSelectedException();
		}

		return $this->currentDomainConfig;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Domain\DomainConfig $domainConfig
	 */
	private function switchDomain(DomainConfig $domainConfig) {
		$this->queueDomainConfig[] = $this->currentDomainConfig;
		$this->currentDomainConfig = $domainConfig;
	}

}
