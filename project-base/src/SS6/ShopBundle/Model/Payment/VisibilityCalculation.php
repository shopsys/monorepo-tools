<?php

namespace SS6\ShopBundle\Model\Payment;

class VisibilityCalculation {
	
	/**
	 * @param \SS6\ShopBundle\Model\Payment\Payment[] $allPayments
	 * @return \SS6\ShopBundle\Model\Payment\Payment[]
	 */
	public function findAllVisible(array $allPayments) {
		$visiblePayments = array();
		foreach ($allPayments as $payment) {
			/* @var $payment \SS6\ShopBundle\Model\Payment\Payment */
			if ($payment->isVisible()) {
				$visiblePayments[] = $payment;
			}
		}

		return $visiblePayments;
	}
}
