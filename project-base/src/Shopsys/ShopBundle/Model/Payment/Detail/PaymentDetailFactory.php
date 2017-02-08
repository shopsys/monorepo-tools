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
			$this->getIndependentPrices($payment)
		);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Payment\Payment[] $payments
	 * @return \SS6\ShopBundle\Model\Payment\Detail\PaymentDetail[]
	 */
	public function createDetailsForPayments(array $payments) {
		$details = [];

		foreach ($payments as $payment) {
			$details[] = $this->createDetailForPayment($payment);
		}

		return $details;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Payment\Payment $payment
	 * @return \SS6\ShopBundle\Model\Pricing\Price
	 */
	private function getIndependentPrices(Payment $payment) {
		$prices = [];
		foreach ($payment->getPrices() as $paymentInputPrice) {
			$currency = $paymentInputPrice->getCurrency();
			$prices[$currency->getId()] = $this->paymentPriceCalculation->calculateIndependentPrice($payment, $currency);
		}
		return $prices;
	}

}
