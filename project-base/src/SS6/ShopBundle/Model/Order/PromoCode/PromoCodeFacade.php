<?php

namespace SS6\ShopBundle\Model\Order\PromoCode;

use SS6\ShopBundle\Model\Setting\Setting;
use SS6\ShopBundle\Model\Setting\SettingValue;

class PromoCodeFacade {

	const PROMO_CODE_SETTING_KEY = 'promoCode';
	const PROMO_CODE_PERCENT_SETTING_KEY = 'promoCodePercent';

	/**
	 * @var \SS6\ShopBundle\Model\Setting\Setting
	 */
	private $setting;

	public function __construct(Setting $setting) {
		$this->setting = $setting;
	}

	/**
	 * @return string|null
	 */
	public function getPromoCode() {
		return $this->setting->get(self::PROMO_CODE_SETTING_KEY, SettingValue::DOMAIN_ID_COMMON);
	}

	/**
	 * @param string|null $code
	 * @param float|null $percent
	 */
	public function editPromoCode($code, $percent) {
		$this->setting->set(self::PROMO_CODE_SETTING_KEY, $code, SettingValue::DOMAIN_ID_COMMON);
		$this->setting->set(self::PROMO_CODE_PERCENT_SETTING_KEY, $percent, SettingValue::DOMAIN_ID_COMMON);
	}

	/**
	 * @return float|null
	 */
	public function getPromoCodePercent() {
		return $this->setting->get(self::PROMO_CODE_PERCENT_SETTING_KEY, SettingValue::DOMAIN_ID_COMMON);
	}

}
