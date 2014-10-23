<?php

namespace SS6\ShopBundle\Model\Transport;

use SS6\ShopBundle\Model\Transport\TransportRepository;

class IndependentTransportVisibilityCalculation {

	/**
	 * @var \SS6\ShopBundle\Model\Transport\TransportRepository
	 */
	private $transportRepository;

	public function __construct(
		TransportRepository $transportRepository
	) {
		$this->transportRepository = $transportRepository;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Transport\Transport $transport
	 * @param int $domainId
	 * @return boolean
	 */
	public function isIndependentlyVisible(Transport $transport, $domainId) {
		if ($transport->isHidden()) {
			return false;
		}

		if (!$this->isOnDomain($transport, $domainId)) {
			return false;
		}

		return true;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Transport\Transport $transport
	 * @param int $domainId
	 * @return boolean
	 */
	private function isOnDomain(Transport $transport, $domainId) {
		$transportDomains = $this->transportRepository->getTransportDomainsByTransport($transport);
		foreach ($transportDomains as $transportDomain) {
			if ($transportDomain->getDomainId() === $domainId) {
				return true;
			}
		}

		return false;
	}

}
