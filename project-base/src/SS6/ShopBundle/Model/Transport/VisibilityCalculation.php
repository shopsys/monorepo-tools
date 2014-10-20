<?php

namespace SS6\ShopBundle\Model\Transport;

use SS6\ShopBundle\Model\Transport\TransportRepository;

class VisibilityCalculation {

	/**
	 * @var \SS6\ShopBundle\Model\Transport\TransportRepository
	 */
	private $transportRepository;

	public function __construct(TransportRepository $transportRepository) {
		$this->transportRepository = $transportRepository;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Transport\Transport $transport
	 * @param \SS6\ShopBundle\Model\Payment\Payment[] $allPaymentsOnDomain
	 * @param int $domainId
	 * @return boolean
	 */
	public function isVisible(Transport $transport, array $allPaymentsOnDomain, $domainId) {
		if (!$transport->isHidden() && $this->existsNotHiddenPaymentWithTransport($allPaymentsOnDomain, $transport)) {
			return $this->isOnDomain($transport, $domainId);
		} else {
			return false;
		}
	}

	/**
	 * @param \SS6\ShopBundle\Model\Payment\Payment[] $payments
	 * @param \SS6\ShopBundle\Model\Transport\Transport $transport
	 * @return boolean
	 */
	private function existsNotHiddenPaymentWithTransport(array $payments, Transport $transport) {
		foreach ($payments as $payment) {
			/* @var $payment \SS6\ShopBundle\Model\Payment\Payment */
			if (!$payment->isHidden() && $payment->getTransports()->contains($transport)) {
				return true;
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

	/**
	 * @param \SS6\ShopBundle\Model\Transport\Transport $transport
	 * @param int $domainId
	 * @return boolean
	 */
	public function isOnDomain(Transport $transport, $domainId) {
		$transportDomains = $this->transportRepository->getTransportDomainsByTransport($transport);
		foreach ($transportDomains as $transportDomain) {
			if ($transportDomain->getDomainId() === $domainId) {
				return true;
			}
		}

		return false;
	}

}
