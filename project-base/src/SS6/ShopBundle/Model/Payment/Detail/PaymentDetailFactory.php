<?php

namespace SS6\ShopBundle\Model\Payment\Detail;

use SS6\ShopBundle\Model\Payment\Payment;
use SS6\ShopBundle\Model\Payment\PaymentPriceCalculation;

class PaymentDetailFactory {

	/**
	 * @var \SS6\ShopBundle\Model\Payment\PaymentPriceCalculation
	 */
	private $paymentPriceCalculation;

	/**
	 * @param \SS6\ShopBundle\Model\Payment\PaymentPriceCalculation $paymentPriceCalculation
	 */
	public function __construct(PaymentPriceCalculation $paymentPriceCalculation) {
		$this->paymentPriceCalculation = $paymentPriceCalculation;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Payment\Payment $payment
	 * @return \SS6\ShopBundle\Model\Payment\Detail\PaymentDetail
	 */
	public function createDetailForPayment(Payment $payment) {
		return new PaymentDetail(
			$payment,
			$this->getPrice($payment)
		);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Payment\Payment[] $payments
	 * @return \SS6\ShopBundle\Model\Payment\Detail\PaymentDetail[]
	 */
	public function createDetailsForPayments(array $payments) {
		$details = array();

		foreach ($payments as $payment) {
			$details[] = $this->createDetailForPayment($payment);
		}

		return $details;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Payment\Payment $payment
	 * @return \SS6\ShopBundle\Model\Pricing\Price
	 */
	private function getPrice(Payment $payment) {
		return $this->paymentPriceCalculation->calculatePrice($payment);
	}

}
