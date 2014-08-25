<?php

namespace SS6\ShopBundle\Model\Transport\Detail;

use SS6\ShopBundle\Model\Transport\PriceCalculation;
use SS6\ShopBundle\Model\Transport\Transport;

class Factory {

	/**
	 * @var \SS6\ShopBundle\Model\Transport\PriceCalculation
	 */
	private $priceCalculation;

	/**
	 * @param \SS6\ShopBundle\Model\Transport\PriceCalculation $priceCalculation
	 */
	public function __construct(PriceCalculation $priceCalculation) {
		$this->priceCalculation = $priceCalculation;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Transport\Transport $transport
	 * @return \SS6\ShopBundle\Model\Transport\Detail\Detail
	 */
	public function createDetailForTransport(Transport $transport) {
		return new Detail(
			$transport,
			$this->getPrice($transport)
		);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Transport\Transport[] $transports
	 * @return \SS6\ShopBundle\Model\Transport\Detail\Detail[]
	 */
	public function createDetailsForTransports(array $transports) {
		$details = array();

		foreach ($transports as $transport) {
			$details[] = $this->createDetailForTransport($transport);
		}

		return $details;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Transport\Transport $transport
	 * @return \SS6\ShopBundle\Model\Pricing\Price
	 */
	private function getPrice(Transport $transport) {
		return $this->priceCalculation->calculatePrice($transport);
	}

}
