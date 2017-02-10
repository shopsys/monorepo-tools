<?php

namespace Shopsys\ShopBundle\Model\Payment;

use Shopsys\ShopBundle\Model\Payment\Payment;
use Shopsys\ShopBundle\Model\Payment\PaymentPriceRepository;

class PaymentPriceEditFacade {

	/**
	 * @var \Shopsys\ShopBundle\Model\Payment\PaymentPriceRepository
	 */
	private $paymentPriceRepository;

	/**
	 * @param \Shopsys\ShopBundle\Model\Payment\PaymentPriceRepository $paymentPriceRepository
	 */
	public function __construct(PaymentPriceRepository $paymentPriceRepository) {
		$this->paymentPriceRepository = $paymentPriceRepository;
	}

	/**
	 * @param \Shopsys\ShopBundle\Model\Payment\Payment $payment
	 * @return \Shopsys\ShopBundle\Model\Payment\PaymentPrice[]
	 */
	public function getAllByPayment(Payment $payment) {
		return $this->paymentPriceRepository->getAllByPayment($payment);
	}

}
