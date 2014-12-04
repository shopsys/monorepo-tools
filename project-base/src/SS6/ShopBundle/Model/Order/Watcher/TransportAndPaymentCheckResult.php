<?php

namespace SS6\ShopBundle\Model\Order\Watcher;

class TransportAndPaymentCheckResult {

	/**
	 * @var boolean
	 */
	private $transportPriceChanged;

	/**
	 * @var boolean
	 */
	private $paymentPriceChanged;

	/**
	 * @param boolean $transportPriceChanged
	 * @param boolean $paymentPriceChanged
	 */
	public function __construct(
		$transportPriceChanged,
		$paymentPriceChanged
	) {
		$this->transportPriceChanged = $transportPriceChanged;
		$this->paymentPriceChanged = $paymentPriceChanged;
	}

	/**
	 * @return boolean
	 */
	public function isTransportPriceChanged() {
		return $this->transportPriceChanged;
	}

	/**
	 * @return boolean
	 */
	public function isPaymentPriceChanged() {
		return $this->paymentPriceChanged;
	}

}
