<?php

namespace SS6\ShopBundle\Component\Domain;

use SS6\ShopBundle\Component\Setting\Setting;
use Symfony\Component\HttpFoundation\Request;

class Domain {

	const MAIN_ADMIN_DOMAIN_ID = 1;

	/**
	 * @var \SS6\ShopBundle\Component\Domain\Config\DomainConfig|null
	 */
	private $currentDomainConfig;

	/**
	 * @var \SS6\ShopBundle\Component\Domain\Config\DomainConfig[]
	 */
	private $domainConfigs;

	/**
	 * @var \SS6\ShopBundle\Component\Setting\Setting
	 */
	private $setting;

	/**
	 * @param \SS6\ShopBundle\Component\Domain\Config\DomainConfig[] $domainConfigs
	 * @param \SS6\ShopBundle\Component\Setting\Setting $setting
	 */
	public function __construct(array $domainConfigs, Setting $setting) {
		$this->domainConfigs = $domainConfigs;
		$this->setting = $setting;
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
	 * @return bool
	 */
	public function isHttps() {
		return $this->getCurrentDomainConfig()->isHttps();
	}

	/**
	 * @return \SS6\ShopBundle\Component\Domain\Config\DomainConfig[]
	 */
	public function getAll() {
		$domainConfigsWithDataCreated = [];
		foreach ($this->domainConfigs as $domainConfig) {
			$domainId = $domainConfig->getId();
			try {
				$this->setting->getForDomain(Setting::DOMAIN_DATA_CREATED, $domainId);
				$domainConfigsWithDataCreated[] = $domainConfig;
			} catch (\SS6\ShopBundle\Component\Setting\Exception\SettingValueNotFoundException $ex) {
			}
		}

		return $domainConfigsWithDataCreated;
	}

	/**
	 * @return int[]
	 */
	public function getAllIds() {
		$ids = [];
		foreach ($this->getAll() as $domainConfig) {
			$ids[] = $domainConfig->getId();
		}

		return $ids;
	}

	/**
	 * @return \SS6\ShopBundle\Component\Domain\Config\DomainConfig[]
	 */
	public function getAllIncludingDomainConfigsWithoutDataCreated() {
		return $this->domainConfigs;
	}

	/**
	 * @param int $domainId
	 * @return \SS6\ShopBundle\Component\Domain\Config\DomainConfig
	 */
	public function getDomainConfigById($domainId) {
		foreach ($this->domainConfigs as $domainConfig) {
			if ($domainId === $domainConfig->getId()) {
				return $domainConfig;
			}
		}

		throw new \SS6\ShopBundle\Component\Domain\Exception\InvalidDomainIdException();
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

		throw new \SS6\ShopBundle\Component\Domain\Exception\UnableToResolveDomainException($url);
	}

	/**
	 * @return \SS6\ShopBundle\Component\Domain\Config\DomainConfig
	 */
	public function getCurrentDomainConfig() {
		if ($this->currentDomainConfig === null) {
			throw new \SS6\ShopBundle\Component\Domain\Exception\NoDomainSelectedException();
		}

		return $this->currentDomainConfig;
	}

}
