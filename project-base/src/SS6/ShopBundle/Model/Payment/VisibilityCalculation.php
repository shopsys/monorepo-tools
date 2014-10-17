<?php

namespace SS6\ShopBundle\Model\Payment;

use SS6\ShopBundle\Model\Transport\VisibilityCalculation as TransportVisibilityCalculation;

class VisibilityCalculation {

	/**
	 * @var \SS6\ShopBundle\Model\Transport\VisibilityCalculation
	 */
	private $transportVisibilityCalculation;

	public function __construct(TransportVisibilityCalculation $transportVisibilityCalculation) {
		$this->transportVisibilityCalculation = $transportVisibilityCalculation;
	}

	
	/**
	 * @param \SS6\ShopBundle\Model\Payment\Payment[] $payments
	 * @return \SS6\ShopBundle\Model\Payment\Payment[]
	 */
	public function filterVisible(array $payments, $domainId) {
		$visiblePayments = array();
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
	 * @return boolean
	 */
	private function isVisible(Payment $payment, $domainId) {
		if ($payment->isHidden()) {
			return false;
		}

		foreach ($payment->getTransports() as $transport) {
			/* @var $transport \SS6\ShopBundle\Model\Transport\Transport */
			if (!$transport->isHidden() && $this->transportVisibilityCalculation->isOnDomain($transport, $domainId)) {
				return true;
			}
		}

		return false;
	}
}
