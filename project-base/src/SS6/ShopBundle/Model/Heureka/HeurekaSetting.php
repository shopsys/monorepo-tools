<?php

namespace SS6\ShopBundle\Model\Heureka;

use SS6\ShopBundle\Component\Setting\Setting;

class HeurekaSetting {

	const HEUREKA_API_KEY = 'heurekaApiKey';

	/**
	 * @var \SS6\ShopBundle\Component\Setting\Setting
	 */
	private $setting;

	/**
	 * @param \SS6\ShopBundle\Component\Setting\Setting $setting
	 */
	public function __construct(Setting $setting) {
		$this->setting = $setting;
	}

	/**
	 * @param int $domainId
	 * @return string
	 */
	public function getApiKeyByDomainId($domainId) {
		return $this->setting->getForDomain(self::HEUREKA_API_KEY, $domainId);
	}

	/**
	 * @param string $apiKey
	 * @param int $domainId
	 */
	public function setApiKeyForDomain($apiKey, $domainId) {
		$this->setting->setForDomain(self::HEUREKA_API_KEY, $apiKey, $domainId);
	}

	/**
	 * @param int $domainId
	 * @return bool
	 */
	public function isHeurekaShopCertificationActivated($domainId) {
		return !empty($this->setting->getForDomain(self::HEUREKA_API_KEY, $domainId));
	}
}
