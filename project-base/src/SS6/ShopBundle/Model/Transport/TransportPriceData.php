<?php

namespace SS6\ShopBundle\Model\Transport;

use SS6\ShopBundle\Model\Pricing\Currency\Currency;

class TransportPriceData {

	/**
	 * @var SS6\ShopBundle\Model\Pricing\Currency\Currency
	 */
	private $currency;

	/**
	 * @var string
	 */
	private $price;

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Currency\Currency $currency
	 * @param string $price
	 */
	public function __construct(Currency $currency, $price) {
		$this->currency = $currency;
		$this->price = $price;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Pricing\Currency\Currency
	 */
	public function getCurrency() {
		return $this->currency;
	}

	/**
	 * @return string
	 */
	public function getPrice() {
		return $this->price;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Currency\Currency $currency
	 */
	public function setCurrency(Currency $currency) {
		$this->currency = $currency;
	}

	/**
	 * @param string $price
	 */
	public function setPrice($price) {
		$this->price = $price;
	}

}
