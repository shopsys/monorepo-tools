<?php

namespace SS6\ShopBundle\Model\Transport;

use SS6\ShopBundle\Model\Transport\TransportData;

class TransportEditData {

	/**
	 * @var \SS6\ShopBundle\Model\Transport\TransportData
	 */
	public $transportData;

	/**
	 * @var string[currencyId]
	 */
	public $prices;

	/**
	 * @param \SS6\ShopBundle\Model\Transport\TransportData $transportData
	 * @param array $prices
	 */
	public function __construct(TransportData $transportData = null, array $prices = []) {
		if ($transportData !== null) {
			$this->transportData = $transportData;
		} else {
			$this->transportData = new TransportData();
		}
		$this->prices = $prices;
	}
}
