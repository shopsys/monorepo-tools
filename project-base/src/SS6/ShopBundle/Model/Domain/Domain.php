<?php

namespace SS6\ShopBundle\Model\Domain;

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
	public function getLocale() {
		return $this->getCurrentConfig()->getLocale();
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->getCurrentConfig()->getName();
	}

	/**
	 * @return string
	 */
	public function getTemplatesDirectory() {
		return $this->getCurrentConfig()->getTemplatesDirectory();
	}

	/**
	 * @return \SS6\ShopBundle\Model\Domain\Config\DomainConfig[]
	 */
	public function getAll() {
		return $this->domainConfigs;
	}

	/**
	 * @param int $domainId
	 * @return \SS6\ShopBundle\Model\Domain\Config\DomainConfig
	 */
	public function getDomainConfigById($domainId) {
		foreach ($this->domainConfigs as $domainConfig) {
			if ($domainId === $domainConfig->getId()) {
				return $domainConfig;
			}
		}

		throw new \SS6\ShopBundle\Model\Domain\Exception\InvalidDomainIdException();
	}

	/**
	 * @param int $domainId
	 */
	public function switchDomainById($domainId) {
		$this->currentDomainConfig = $this->getDomainConfigById($domainId);
	}

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function switchDomainByRequest(Request $request) {
		$url = $request->getSchemeAndHttpHost();

		foreach ($this->domainConfigs as $domainConfig) {
			if ($domainConfig->getUrl() === $url) {
				$this->currentDomainConfig = $domainConfig;
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

}
