<?php

namespace Shopsys\ShopBundle\Model\Payment;

use Shopsys\ShopBundle\Model\Payment\PaymentData;

class PaymentEditData {

	/**
	 * @var \Shopsys\ShopBundle\Model\Payment\PaymentData
	 */
	public $paymentData;

	/**
	 * @var string[currencyId]
	 */
	public $prices;

	/**
	 * @param \Shopsys\ShopBundle\Model\Payment\PaymentData $paymentData
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
