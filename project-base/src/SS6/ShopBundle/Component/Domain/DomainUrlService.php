<?php

namespace SS6\ShopBundle\Component\Domain;

use SS6\ShopBundle\Component\Domain\Config\DomainConfig;
use SS6\ShopBundle\Component\Setting\Setting;

class DomainUrlService {

	/**
	 * @var \SS6\ShopBundle\Component\Setting\Setting
	 */
	private $setting;

	public function __construct(Setting $setting) {
		$this->setting = $setting;
	}

	/**
	 * @param \SS6\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
	 * @return bool
	 */
	public function isDomainConfigUrlMatchingDomainSettingUrl(DomainConfig $domainConfig) {
		$domainConfigUrl = $domainConfig->getUrl();
		$domainSettingUrl = $this->setting->get(Setting::BASE_URL, $domainConfig->getId());

		return $domainConfigUrl === $domainSettingUrl;
	}

}
