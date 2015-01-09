<?php

namespace SS6\ShopBundle\Model\Pricing\Currency;

use SS6\ShopBundle\Component\Validator;
use SS6\ShopBundle\Model\Pricing\Currency\Currency;

/**
 * @Validator\Auto(entity="SS6\ShopBundle\Model\Pricing\Currency\Currency")
 */
class CurrencyData {

	/**
	 * @var string
	 */
	public $name;

	/**
	 * @var string
	 */
	public $code;

	/**
	 * @var string
	 */
	public $symbol;

	/**
	 * @var string
	 */
	public $exchangeRate;

	/**
	 * @param string|null $name
	 * @param string|null $code
	 * @param string|null $symbol
	 * @param string $exchangeRate
	 */
	public function __construct($name = null, $code = null, $symbol = null, $exchangeRate = Currency::DEFAULT_EXCHANGE_RATE) {
		$this->name = $name;
		$this->code = $code;
		$this->symbol = $symbol;
		$this->exchangeRate = $exchangeRate;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Currency\Currency $currency
	 */
	public function setFromEntity(Currency $currency) {
		$this->name = $currency->getName();
		$this->code = $currency->getCode();
		$this->symbol = $currency->getSymbol();
		$this->exchangeRate = $currency->getExchangeRate();
	}

}
