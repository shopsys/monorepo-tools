<?php

namespace SS6\ShopBundle\Model\Payment;

use SS6\ShopBundle\Model\Payment\Payment;
use SS6\ShopBundle\Model\Payment\PaymentPriceRepository;

class PaymentPriceEditFacade {

	/**
	 * @var \SS6\ShopBundle\Model\Payment\PaymentPriceRepository
	 */
	private $paymentPriceRepository;

	/**
	 * @param \SS6\ShopBundle\Model\Payment\PaymentPriceRepository $paymentPriceRepository
	 */
	public function __construct(PaymentPriceRepository $paymentPriceRepository) {
		$this->paymentPriceRepository = $paymentPriceRepository;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Payment\Payment $payment
	 * @return \SS6\ShopBundle\Model\Payment\PaymentPrice[]
	 */
	public function getAllByPayment(Payment $payment) {
		return $this->paymentPriceRepository->getAllByPayment($payment);
	}

}
