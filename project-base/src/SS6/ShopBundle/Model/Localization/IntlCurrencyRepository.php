<?php

namespace SS6\ShopBundle\Model\Localization;

use CommerceGuys\Intl\Currency\CurrencyRepository as BaseCurrencyRepository;
use SS6\ShopBundle\Model\Pricing\Currency\CurrencyRepository;

class IntlCurrencyRepository extends BaseCurrencyRepository {

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Currency\Currency[code]
	 */
	private $currencyByCodeCache;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Currency\CurrencyRepository
	 */
	private $currencyRepository;

	public function __construct(CurrencyRepository $currencyRepository) {
		parent::__construct();

		$this->currencyByCodeCache = [];
		$this->currencyRepository = $currencyRepository;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get($currencyCode, $locale = null, $fallbackLocale = null) {
		$intlCurrency = parent::get($currencyCode, $locale, $fallbackLocale);

		return $intlCurrency;
	}

	/**
	 * {@inheritDoc}
	 * @return \CommerceGuys\Intl\Currency\CurrencyInterface[]
	 */
	public function getAll($locale = null, $fallbackLocale = null) {
		$intlCurrencies = parent::getAll($locale, $fallbackLocale);

		return $intlCurrencies;
	}

}
