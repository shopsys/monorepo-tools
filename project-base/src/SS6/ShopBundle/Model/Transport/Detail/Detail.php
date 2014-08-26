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
	 * @var boolean
	 */
	private $visible;

	/**
	 * @param \SS6\ShopBundle\Model\Transport\Transport $transport
	 * @param \SS6\ShopBundle\Model\Pricing\Price $price
	 * @param boolean $visible
	 */
	public function __construct(
		Transport $transport,
		Price $price,
		$visible
	) {
		$this->transport = $transport;
		$this->price = $price;
		$this->visible = $visible;
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

	/**
	 * @return boolean
	 */
	public function isVisible() {
		return $this->visible;
	}

}
