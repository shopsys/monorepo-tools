<?php

namespace SS6\ShopBundle\Model\Transport\Detail;

use SS6\ShopBundle\Model\Payment\PaymentRepository;
use SS6\ShopBundle\Model\Transport\Transport;
use SS6\ShopBundle\Model\Transport\TransportPriceCalculation;
use SS6\ShopBundle\Model\Transport\TransportVisibilityCalculation;

class TransportDetailFactory {

	/**
	 * @var \SS6\ShopBundle\Model\Transport\TransportPriceCalculation
	 */
	private $transportPriceCalculation;

	/**
	 * @var \SS6\ShopBundle\Model\Transport\TransportVisibilityCalculation
	 */
	private $transportVisibilityCalculation;

	/**
	 * @param \SS6\ShopBundle\Model\Transport\TransportPriceCalculation $transportPriceCalculation
	 * @param \SS6\ShopBundle\Model\Transport\TransportVisibilityCalculation $transportVisibilityCalculation
	 */
	public function __construct(
		TransportPriceCalculation $transportPriceCalculation,
		TransportVisibilityCalculation $transportVisibilityCalculation
	) {
		$this->transportPriceCalculation = $transportPriceCalculation;
		$this->transportVisibilityCalculation = $transportVisibilityCalculation;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Transport\Transport $transport
	 * @return \SS6\ShopBundle\Model\Transport\Detail\TransportDetail
	 */
	public function createDetailForTransport(Transport $transport) {
		return new TransportDetail(
			$transport,
			$this->getPrice($transport)
		);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Transport\Transport[] $transports
	 * @return \SS6\ShopBundle\Model\Transport\Detail\TransportDetail[]
	 */
	public function createDetailsForTransports(array $transports) {
		$details = array();

		foreach ($transports as $transport) {
			$details[] = new TransportDetail(
				$transport,
				$this->getPrice($transport)
			);
		}

		return $details;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Transport\Transport $transport
	 * @return \SS6\ShopBundle\Model\Pricing\Price
	 */
	private function getPrice(Transport $transport) {
		return $this->transportPriceCalculation->calculatePrice($transport);
	}

}
