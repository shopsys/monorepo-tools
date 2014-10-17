<?php

namespace SS6\ShopBundle\Model\Transport\Detail;

use SS6\ShopBundle\Model\Payment\PaymentRepository;
use SS6\ShopBundle\Model\Transport\PriceCalculation;
use SS6\ShopBundle\Model\Transport\Transport;
use SS6\ShopBundle\Model\Transport\VisibilityCalculation;

class Factory {

	/**
	 * @var \SS6\ShopBundle\Model\Transport\PriceCalculation
	 */
	private $priceCalculation;

	/**
	 * @var \SS6\ShopBundle\Model\Transport\VisibilityCalculation
	 */
	private $visibilityCalculation;

	/**
	 * @param \SS6\ShopBundle\Model\Transport\PriceCalculation $priceCalculation
	 * @param \SS6\ShopBundle\Model\Transport\VisibilityCalculation $visibilityCalculation
	 */
	public function __construct(
		PriceCalculation $priceCalculation,
		VisibilityCalculation $visibilityCalculation
	) {
		$this->priceCalculation = $priceCalculation;
		$this->visibilityCalculation = $visibilityCalculation;
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
			$details[] = new Detail(
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
		return $this->priceCalculation->calculatePrice($transport);
	}

}
