<?php

namespace Shopsys\ShopBundle\Model\Transport\Detail;

use Shopsys\ShopBundle\Model\Transport\Transport;
use Shopsys\ShopBundle\Model\Transport\TransportPriceCalculation;
use Shopsys\ShopBundle\Model\Transport\TransportVisibilityCalculation;

class TransportDetailFactory {

	/**
	 * @var \Shopsys\ShopBundle\Model\Transport\TransportPriceCalculation
	 */
	private $transportPriceCalculation;

	/**
	 * @var \Shopsys\ShopBundle\Model\Transport\TransportVisibilityCalculation
	 */
	private $transportVisibilityCalculation;

	/**
	 * @param \Shopsys\ShopBundle\Model\Transport\TransportPriceCalculation $transportPriceCalculation
	 * @param \Shopsys\ShopBundle\Model\Transport\TransportVisibilityCalculation $transportVisibilityCalculation
	 */
	public function __construct(
		TransportPriceCalculation $transportPriceCalculation,
		TransportVisibilityCalculation $transportVisibilityCalculation
	) {
		$this->transportPriceCalculation = $transportPriceCalculation;
		$this->transportVisibilityCalculation = $transportVisibilityCalculation;
	}

	/**
	 * @param \Shopsys\ShopBundle\Model\Transport\Transport $transport
	 * @return \Shopsys\ShopBundle\Model\Transport\Detail\TransportDetail
	 */
	public function createDetailForTransportWithIndependentPrices(Transport $transport) {
		return new TransportDetail(
			$transport,
			$this->getIndependentPrices($transport)
		);
	}

	/**
	 * @param \Shopsys\ShopBundle\Model\Transport\Transport[] $transports
	 * @return \Shopsys\ShopBundle\Model\Transport\Detail\TransportDetail[]
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
	 * @param \Shopsys\ShopBundle\Model\Transport\Transport $transport
	 * @return \Shopsys\ShopBundle\Model\Pricing\Price
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
