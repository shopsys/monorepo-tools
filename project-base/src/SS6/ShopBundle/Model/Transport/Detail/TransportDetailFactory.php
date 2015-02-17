<?php

namespace SS6\ShopBundle\Model\Transport\Detail;

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
	public function createDetailForTransportWithIndependentPrices(Transport $transport) {
		return new TransportDetail(
			$transport,
			$this->getIndependentPrices($transport)
		);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Transport\Transport[] $transports
	 * @return \SS6\ShopBundle\Model\Transport\Detail\TransportDetail[]
	 */
	public function createDetailsForTransportsWithIndependentPrices(array $transports) {
		$details = [];

		foreach ($transports as $transport) {
			$details[] = new TransportDetail(
				$transport,
				$this->getIndependentPrices($transport)
			);
		}

		return $details;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Transport\Transport $transport
	 * @return \SS6\ShopBundle\Model\Pricing\Price
	 */
	private function getIndependentPrices(Transport $transport) {
		$prices = [];
		foreach ($transport->getPrices() as $transportInputPrice) {
			$currency = $transportInputPrice->getCurrency();
			$prices[$currency->getId()] = $this->transportPriceCalculation->calculateIndependentPrice($transport, $currency);
		}

		return $prices;
	}

}
