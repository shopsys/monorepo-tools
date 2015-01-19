<?php

namespace SS6\ShopBundle\Model\Pricing;

use SS6\ShopBundle\Model\Pricing\Currency\Currency;
use SS6\ShopBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler;
use SS6\ShopBundle\Model\Setting\Setting;
use SS6\ShopBundle\Model\Setting\SettingValue;

class PricingSetting {

	const INPUT_PRICE_TYPE = 'inputPriceType';
	const ROUNDING_TYPE = 'roundingType';
	const DEFAULT_CURRENCY = 'defaultCurrencyId';
	const DEFAULT_DOMAIN_CURRENCY = 'defaultDomainCurrencyId';

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
	 * @var \SS6\ShopBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler
	 */
	private $productPriceRecalculationScheduler;

	public function __construct(
		Setting $setting,
		ProductPriceRecalculationScheduler $productPriceRecalculationScheduler
	) {
		$this->setting = $setting;
		$this->productPriceRecalculationScheduler = $productPriceRecalculationScheduler;
	}

	/**
	 * @return int
	 */
	public function getInputPriceType() {
		return $this->setting->get(self::INPUT_PRICE_TYPE, SettingValue::DOMAIN_ID_COMMON);
	}

	/**
	 * @return int
	 */
	public function getRoundingType() {
		return $this->setting->get(self::ROUNDING_TYPE, SettingValue::DOMAIN_ID_COMMON);
	}

	/**
	 * @return int
	 */
	public function getDefaultCurrencyId() {
		return $this->setting->get(self::DEFAULT_CURRENCY, SettingValue::DOMAIN_ID_COMMON);
	}

	/**
	 * @param int $domainId
	 * @return int
	 */
	public function getDomainDefaultCurrencyIdByDomainId($domainId) {
		return $this->setting->get(self::DEFAULT_DOMAIN_CURRENCY, $domainId);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Currency\Currency $currency
	 */
	public function setDefaultCurrency(Currency $currency) {
		$currency->setExchangeRate(Currency::DEFAULT_EXCHANGE_RATE);
		$this->setting->set(PricingSetting::DEFAULT_CURRENCY, $currency->getId(), SettingValue::DOMAIN_ID_COMMON);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Currency\Currency $currency
	 * @param int $domainId
	 */
	public function setDomainDefaultCurrency(Currency $currency, $domainId) {
		$this->setting->set(PricingSetting::DEFAULT_DOMAIN_CURRENCY, $currency->getId(), $domainId);
	}

	/**
	 * @param int $roundingType
	 */
	public function setRoundingType($roundingType) {
		if (!in_array($roundingType, $this->getRoundingTypes())) {
			throw new \SS6\ShopBundle\Model\Pricing\Exception\InvalidRoundingTypeException(
				sprintf('Rounding type %s is not valid', $roundingType)
			);
		}

		$this->setting->set(self::ROUNDING_TYPE, $roundingType, SettingValue::DOMAIN_ID_COMMON);
		$this->productPriceRecalculationScheduler->scheduleRecalculatePriceForAllProducts();
	}

	/**
	 * @return array
	 */
	public static function getInputPriceTypes() {
		return [
			self::INPUT_PRICE_TYPE_WITHOUT_VAT,
			self::INPUT_PRICE_TYPE_WITH_VAT,
		];
	}

	/**
	 * @return array
	 */
	public static function getRoundingTypes() {
		return [
			self::ROUNDING_TYPE_HUNDREDTHS,
			self::ROUNDING_TYPE_FIFTIES,
			self::ROUNDING_TYPE_INTEGER,
		];
	}

}
