<?php

namespace SS6\ShopBundle\Model\Pricing;

use SS6\ShopBundle\Model\Pricing\InputPriceFacade;
use SS6\ShopBundle\Model\Setting\Setting3;

class PricingSetting {

	const INPUT_PRICE_TYPE = 'inputPriceType';

	const INPUT_PRICE_TYPE_WITH_VAT = 1;
	const INPUT_PRICE_TYPE_WITHOUT_VAT = 2;
	
	/**
	 * @var \SS6\ShopBundle\Model\Setting\Setting3
	 */
	private $setting;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\InputPriceFacade
	 */
	private $inputPriceFacade;

	/**
	 * @param \SS6\ShopBundle\Model\Setting\Setting3 $setting
	 * @param \SS6\ShopBundle\Model\Pricing\InputPriceFacade $inputPriceFacade
	 */
	public function __construct(Setting3 $setting, InputPriceFacade $inputPriceFacade) {
		$this->setting = $setting;
		$this->inputPriceFacade = $inputPriceFacade;
	}

	/**
	 * @param int $inputPriceType
	 * @throws \SS6\ShopBundle\Model\Pricing\Exception\InvalidInputPriceTypeException
	 */
	public function scheduleSetInputPriceType($inputPriceType) {
		if (!in_array($inputPriceType, array_keys($this->getInputPriceTypes()))) {
			throw new \SS6\ShopBundle\Model\Pricing\Exception\InvalidInputPriceTypeException();
		}

		$currentInputPriceType = $this->setting->get(Setting3::INPUT_PRICE_TYPE);

		if ($currentInputPriceType != $inputPriceType) {
			switch ($inputPriceType) {
				case self::INPUT_PRICE_TYPE_WITHOUT_VAT:
					$this->inputPriceFacade->scheduleSetInputPricesWithoutVat();
					break;

				case self::INPUT_PRICE_TYPE_WITH_VAT:
					$this->inputPriceFacade->scheduleSetInputPricesWithVat();
					break;
			}
		}
	}

	/**
	 * @return int
	 */
	public function getInputPriceType() {
		return $this->setting->get(Setting3::INPUT_PRICE_TYPE);
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
