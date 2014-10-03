<?php

namespace SS6\ShopBundle\Model\Transport;

class VisibilityCalculation {

	/**
	 * @param \SS6\ShopBundle\Model\Transport\Transport $transport
	 * @param \SS6\ShopBundle\Model\Payment\Payment[] $allPayments
	 * @return boolean
	 */
	public function isVisible(Transport $transport, array $allPayments) {
		if (!$transport->isHidden()) {
			return $this->existsVisiblePaymentWithTransport($allPayments, $transport);
		} else {
			return false;
		}
	}

	/**
	 * @param \SS6\ShopBundle\Model\Payment\Payment[] $allPayments
	 * @param \SS6\ShopBundle\Model\Transport\Transport $transport
	 * @return boolean
	 */
	private function existsVisiblePaymentWithTransport(array $allPayments, Transport $transport) {
		foreach ($allPayments as $payment) {
			/* @var $payment \SS6\ShopBundle\Model\Payment\Payment */
			if ($payment->isVisible() && $payment->getTransports()->contains($transport)) {
				return true;
			}
		}
		return false;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Transport\Transport[] $transports
	 * @param array $visiblePayments
	 * @return array
	 */
	public function findAllVisible(array $transports) {
		$transportsData = $this->findAll($transports);
		$visibleTransports = array();
		foreach ($transportsData as $transportData) {
			if ($transportData['visible']) {
				$visibleTransports[] = $transportData['entity'];
			}
		}

		return $visibleTransports;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Transport\Transport[] $transports
	 * @param \SS6\ShopBundle\Model\Payment\Payment[] $visiblePayments
	 * @return array
	 */
	public function findAll(array $transports, array $visiblePayments) {
		$transportsData = [];

		foreach ($transports as $transport) {
			/* @var $transport \SS6\ShopBundle\Model\Transport\Transport */
			$visible = $this->isVisible($transport, $visiblePayments);
			$transportsData[] = array(
				'entity' => $transport,
				'visible' => $visible,
			);
		}
		return $transportsData;
	}

}
