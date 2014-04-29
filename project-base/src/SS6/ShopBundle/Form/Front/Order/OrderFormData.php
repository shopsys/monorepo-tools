<?php

namespace SS6\ShopBundle\Form\Front\Order;

use SS6\ShopBundle\Model\Payment\Payment;
use SS6\ShopBundle\Model\Transport\Transport;

class OrderFormData {
	/**
	 * @var \SS6\ShopBundle\Model\Transport\Transport
	 */
	private $transport;

	/**
	 * @var \SS6\ShopBundle\Model\Payment\Payment
	 */
	private $payment;

	/**
	 * @return \SS6\ShopBundle\Model\Transport\Transport
	 */
	public function getTransport() {
		return $this->transport;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Payment\Payment
	 */
	public function getPayment() {
		return $this->payment;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Transport\Transport $transport
	 */
	public function setTransport(Transport $transport) {
		$this->transport = $transport;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Payment\Payment $payment
	 */
	public function setPayment(Payment $payment) {
		$this->payment = $payment;
	}
}
