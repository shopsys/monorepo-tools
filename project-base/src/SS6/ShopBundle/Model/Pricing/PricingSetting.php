<?php

namespace SS6\ShopBundle\Model\Pricing;

use SS6\ShopBundle\Model\Setting\Setting;

class PricingSetting {

	const INPUT_PRICE_TYPE = 'inputPriceType';
	const ROUNDING_TYPE = 'roundingType';

	const INPUT_PRICE_TYPE_WITH_VAT = 1;
	const INPUT_PRICE_TYPE_WITHOUT_VAT = 2;

	const ROUNDING_TYPE_HUNDREDTHS = 1;
	const ROUNDING_TYPE_FIFTIES = 2;
	const ROUNDING_TYPE_INTEGER = 3;

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
	 * @return int
	 */
	public function getRoundingType() {
		return $this->setting->get(self::ROUNDING_TYPE);
	}

	/**
	 * @param int $roundingType
	 */
	public function setRoundingType($roundingType) {
		if (!array_key_exists($roundingType, $this->getRoundingTypes())) {
			throw new \SS6\ShopBundle\Model\Pricing\Exception\InvalidRoundingTypeException();
		}

		$this->setting->set(self::ROUNDING_TYPE, $roundingType);
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

	/**
	 * @return array
	 */
	public static function getRoundingTypes() {
		return array(
			self::ROUNDING_TYPE_HUNDREDTHS => 'Na setiny (haléře)',
			self::ROUNDING_TYPE_FIFTIES => 'Na padesátníky',
			self::ROUNDING_TYPE_INTEGER => 'Na celá čísla (koruny)',
		);
	}

}
