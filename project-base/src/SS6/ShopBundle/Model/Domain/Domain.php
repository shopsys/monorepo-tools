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
		return $this->getCurrentDomainConfig()->getId();
	}

	/**
	 * @return string
	 */
	public function getLocale() {
		return $this->getCurrentDomainConfig()->getLocale();
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->getCurrentDomainConfig()->getName();
	}

	/**
	 * @return string
	 */
	public function getUrl() {
		return $this->getCurrentDomainConfig()->getUrl();
	}

	/**
	 * @return string
	 */
	public function getTemplatesDirectory() {
		return $this->getCurrentDomainConfig()->getTemplatesDirectory();
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
		$url = $request->getUriForPath('');

		foreach ($this->domainConfigs as $domainConfig) {
			if ($domainConfig->getUrl() === $url) {
				$this->currentDomainConfig = $domainConfig;
				return;
			}
		}

		throw new \SS6\ShopBundle\Model\Domain\Exception\UnableToResolveDomainException();
	}

	/**
	 * @return \SS6\ShopBundle\Model\Domain\Config\DomainConfig
	 */
	public function getCurrentDomainConfig() {
		if ($this->currentDomainConfig === null) {
			throw new \SS6\ShopBundle\Model\Domain\Exception\NoDomainSelectedException();
		}

		return $this->currentDomainConfig;
	}

}
