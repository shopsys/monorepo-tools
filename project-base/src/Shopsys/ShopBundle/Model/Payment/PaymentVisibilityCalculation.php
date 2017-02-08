<?php

namespace SS6\ShopBundle\Model\Payment;

use SS6\ShopBundle\Model\Transport\IndependentTransportVisibilityCalculation;

class PaymentVisibilityCalculation {

	/**
	 * @var \SS6\ShopBundle\Model\Payment\IndependentPaymentVisibilityCalculation
	 */
	private $independentPaymentVisibilityCalculation;

	/**
	 * @var \SS6\ShopBundle\Model\Transport\IndependentTransportVisibilityCalculation
	 */
	private $independentTransportVisibilityCalculation;

	public function __construct(
		IndependentPaymentVisibilityCalculation $independentPaymentVisibilityCalculation,
		IndependentTransportVisibilityCalculation $independentTransportVisibilityCalculation
	) {
		$this->independentPaymentVisibilityCalculation = $independentPaymentVisibilityCalculation;
		$this->independentTransportVisibilityCalculation = $independentTransportVisibilityCalculation;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Payment\Payment[] $payments
	 * @return \SS6\ShopBundle\Model\Payment\Payment[]
	 */
	public function filterVisible(array $payments, $domainId) {
		$visiblePayments = [];
		foreach ($payments as $payment) {
			if ($this->isVisible($payment, $domainId)) {
				$visiblePayments[] = $payment;
			}
		}

		return $visiblePayments;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Payment\Payment $payment
	 * @param int $domainId
	 * @return bool
	 */
	private function isVisible(Payment $payment, $domainId) {
		if (!$this->independentPaymentVisibilityCalculation->isIndependentlyVisible($payment, $domainId)) {
			return false;
		}

		return $this->hasIndependentlyVisibleTransport($payment, $domainId);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Payment\Payment $payment
	 * @param int $domainId
	 * @return bool
	 */
	private function hasIndependentlyVisibleTransport(Payment $payment, $domainId) {
		foreach ($payment->getTransports() as $transport) {
			/* @var $transport \SS6\ShopBundle\Model\Transport\Transport */
			if ($this->independentTransportVisibilityCalculation->isIndependentlyVisible($transport, $domainId)) {
				return true;
			}
		}

		return false;
	}

}
