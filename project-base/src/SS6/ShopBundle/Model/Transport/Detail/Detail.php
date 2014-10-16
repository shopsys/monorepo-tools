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
	private $price;

	/**
	 * @param \SS6\ShopBundle\Model\Transport\Transport $transport
	 * @param \SS6\ShopBundle\Model\Pricing\Price $price
	 */
	public function __construct(
		Transport $transport,
		Price $price
	) {
		$this->transport = $transport;
		$this->price = $price;
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
	public function getPrice() {
		return $this->price;
	}

}
