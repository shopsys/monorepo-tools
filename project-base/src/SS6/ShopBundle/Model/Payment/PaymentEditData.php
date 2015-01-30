<?php

namespace SS6\ShopBundle\Model\Payment;

use SS6\ShopBundle\Model\Payment\PaymentData;

class PaymentEditData {

	/**
	 * @var \SS6\ShopBundle\Model\Payment\PaymentData
	 */
	public $paymentData;

	/**
	 * @var string[currencyId]
	 */
	public $prices;

	/**
	 * @param \SS6\ShopBundle\Model\Payment\PaymentData $paymentData
	 * @param array $prices
	 */
	public function __construct(PaymentData $paymentData = null, array $prices = []) {
		if ($paymentData !== null) {
			$this->paymentData = $paymentData;
		} else {
			$this->paymentData = new PaymentData();
		}
		$this->prices = $prices;
	}
}
