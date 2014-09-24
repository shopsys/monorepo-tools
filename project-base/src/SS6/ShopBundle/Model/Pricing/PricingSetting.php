<?php

namespace SS6\ShopBundle\Model\Pricing;

use SS6\ShopBundle\Model\Setting\Setting;

class PricingSetting {

	const INPUT_PRICE_TYPE = 'inputPriceType';

	const INPUT_PRICE_TYPE_WITH_VAT = 1;
	const INPUT_PRICE_TYPE_WITHOUT_VAT = 2;
	
	/**
	 * @var \SS6\ShopBundle\Model\Setting\Setting
	 */
	private $setting;

	/**
	 * @param \SS6\ShopBundle\Model\Setting\Setting $setting
	 */
	public function __construct(Setting $setting) {
		$this->setting = $setting;
	}

	/**
	 * @return int
	 */
	public function getInputPriceType() {
		return $this->setting->get(self::INPUT_PRICE_TYPE);
	}

	/**
	 * @return array
	 */
	public static function getInputPriceTypes() {
		return array(
			self::INPUT_PRICE_TYPE_WITHOUT_VAT => 'Bez DPH',
			self::INPUT_PRICE_TYPE_WITH_VAT => 'S DPH',
		);
	}

}
