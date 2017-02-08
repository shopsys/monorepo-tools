<?php

namespace SS6\ShopBundle\Model\Payment\Detail;

use SS6\ShopBundle\Model\Payment\Payment;

class PaymentDetail {

	/**
	 * @var \SS6\ShopBundle\Model\Payment\Payment
	 */
	private $payment;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Price[currencyId]
	 */
	private $basePrices;

	/**
	 * @param \SS6\ShopBundle\Model\Payment\Payment $payment
	 * @param \SS6\ShopBundle\Model\Pricing\Price[currencyId] $basePrices
	 */
	public function __construct(Payment $payment, array $basePrices) {
		$this->payment = $payment;
		$this->basePrices = $basePrices;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Payment\Payment
	 */
	public function getPayment() {
		return $this->payment;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Pricing\Price[currencyId]
	 */
	public function getBasePrices() {
		return $this->basePrices;
	}

}
