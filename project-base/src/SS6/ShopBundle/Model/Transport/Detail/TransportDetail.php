<?php

namespace SS6\ShopBundle\Model\Transport\Detail;

use SS6\ShopBundle\Model\Transport\Transport;

class TransportDetail {

	/**
	 * @var \SS6\ShopBundle\Model\Transport\Transport
	 */
	private $transport;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Price[currencyId]
	 */
	private $basePrices;

	/**
	 * @param \SS6\ShopBundle\Model\Transport\Transport $transport
	 * @param \SS6\ShopBundle\Model\Pricing\Price[currencyId] $basePrices
	 */
	public function __construct(
		Transport $transport,
		array $basePrices
	) {
		$this->transport = $transport;
		$this->basePrices = $basePrices;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Transport\Transport
	 */
	public function getTransport() {
		return $this->transport;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Pricing\Price[currencyId]
	 */
	public function getBasePrices() {
		return $this->basePrices;
	}

}
