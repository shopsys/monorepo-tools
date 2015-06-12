<?php

namespace SS6\ShopBundle\Model\Transport;

use SS6\ShopBundle\Model\Payment\IndependentPaymentVisibilityCalculation;

class TransportVisibilityCalculation {

	/**
	 * @var \SS6\ShopBundle\Model\Transport\IndependentTransportVisibilityCalculation
	 */
	private $independentTransportVisibilityCalculation;

	/**
	 * @var \SS6\ShopBundle\Model\Payment\IndependentPaymentVisibilityCalculation
	 */
	private $independentPaymentVisibilityCalculation;

	public function __construct(
		IndependentTransportVisibilityCalculation $independentTransportVisibilityCalculation,
		IndependentPaymentVisibilityCalculation $independentPaymentVisibilityCalculation
	) {
		$this->independentTransportVisibilityCalculation = $independentTransportVisibilityCalculation;
		$this->independentPaymentVisibilityCalculation = $independentPaymentVisibilityCalculation;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Transport\Transport $transport
	 * @param \SS6\ShopBundle\Model\Payment\Payment[] $allPaymentsOnDomain
	 * @param int $domainId
	 * @return bool
	 */
	public function isVisible(Transport $transport, array $allPaymentsOnDomain, $domainId) {
		if (!$this->independentTransportVisibilityCalculation->isIndependentlyVisible($transport, $domainId)) {
			return false;
		}

		return $this->existsIndependentlyVisiblePaymentWithTransport($allPaymentsOnDomain, $transport, $domainId);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Payment\Payment[] $payments
	 * @param \SS6\ShopBundle\Model\Transport\Transport $transport
	 * @param int $domainId
	 * @return bool
	 */
	private function existsIndependentlyVisiblePaymentWithTransport(array $payments, Transport $transport, $domainId) {
		foreach ($payments as $payment) {
			/* @var $payment \SS6\ShopBundle\Model\Payment\Payment */
			if ($this->independentPaymentVisibilityCalculation->isIndependentlyVisible($payment, $domainId)) {
				if ($payment->getTransports()->contains($transport)) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Transport\Transport[] $transports
	 * @param \SS6\ShopBundle\Model\Payment\Payment[] $visiblePaymentsOnDomain
	 * @param int $domainId
	 * @return \SS6\ShopBundle\Model\Transport\Transport[]
	 */
	public function filterVisible(array $transports, array $visiblePaymentsOnDomain, $domainId) {
		$visibleTransports = [];

		foreach ($transports as $transport) {
			if ($this->isVisible($transport, $visiblePaymentsOnDomain, $domainId)) {
				$visibleTransports[] = $transport;
			}
		}

		return $visibleTransports;
	}

}
