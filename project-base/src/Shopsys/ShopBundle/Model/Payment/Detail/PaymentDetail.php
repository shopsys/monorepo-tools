<?php

namespace Shopsys\ShopBundle\Model\Payment\Detail;

use Shopsys\ShopBundle\Model\Payment\Payment;

class PaymentDetail {

	/**
	 * @var \Shopsys\ShopBundle\Model\Payment\Payment
	 */
	private $payment;

	/**
	 * @var \Shopsys\ShopBundle\Model\Pricing\Price[currencyId]
	 */
	private $basePrices;

	/**
	 * @param \Shopsys\ShopBundle\Model\Payment\Payment $payment
	 * @param \Shopsys\ShopBundle\Model\Pricing\Price[currencyId] $basePrices
	 */
	public function __construct(Payment $payment, array $basePrices) {
		$this->payment = $payment;
		$this->basePrices = $basePrices;
	}

	/**
	 * @return \Shopsys\ShopBundle\Model\Payment\Payment
	 */
	public function getPayment() {
		return $this->payment;
	}

	/**
	 * @return \Shopsys\ShopBundle\Model\Pricing\Price[currencyId]
	 */
	public function getBasePrices() {
		return $this->basePrices;
	}

}
