<?php

namespace SS6\ShopBundle\Model\Transport\Detail;

use SS6\ShopBundle\Model\Pricing\Price;
use SS6\ShopBundle\Model\Transport\Transport;

class Detail {

	/**
	 * @var \SS6\ShopBundle\Model\Transport\Transport
	 */
	private $transport;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Price
	 */
	private $basePrice;

	/**
	 * @param \SS6\ShopBundle\Model\Transport\Transport $transport
	 * @param \SS6\ShopBundle\Model\Pricing\Price $basePrice
	 */
	public function __construct(
		Transport $transport,
		Price $basePrice
	) {
		$this->transport = $transport;
		$this->basePrice = $basePrice;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Transport\Transport
	 */
	public function getTransport() {
		return $this->transport;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Pricing\Price
	 */
	public function getBasePrice() {
		return $this->basePrice;
	}

}
