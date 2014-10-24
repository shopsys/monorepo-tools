<?php

namespace SS6\ShopBundle\Model\Payment;

use SS6\ShopBundle\Model\Payment\PaymentRepository;

class IndependentPaymentVisibilityCalculation {

	/**
	 * @var \SS6\ShopBundle\Model\Payment\PaymentRepository
	 */
	private $paymentRepository;

	public function __construct(
		PaymentRepository $paymentRepository
	) {
		$this->paymentRepository = $paymentRepository;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Payment\Payment $payment
	 * @param int $domainId
	 * @return boolean
	 */
	public function isIndependentlyVisible(Payment $payment, $domainId) {
		if ($payment->isHidden()) {
			return false;
		}

		if (!$this->isOnDomain($payment, $domainId)) {
			return false;
		}

		return true;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Payment\Payment $payment
	 * @param int $domainId
	 * @return boolean
	 */
	private function isOnDomain(Payment $payment, $domainId) {
		$paymentDomains = $this->paymentRepository->getPaymentDomainsByPayment($payment);
		foreach ($paymentDomains as $paymentDomain) {
			if ($paymentDomain->getDomainId() === $domainId) {
				return true;
			}
		}

		return false;
	}

}
