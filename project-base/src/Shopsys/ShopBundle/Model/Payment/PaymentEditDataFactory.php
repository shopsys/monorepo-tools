<?php

namespace Shopsys\ShopBundle\Model\Payment;

use Shopsys\ShopBundle\Model\Payment\PaymentEditData;
use Shopsys\ShopBundle\Model\Payment\PaymentEditFacade;
use Shopsys\ShopBundle\Model\Pricing\Vat\VatFacade;

class PaymentEditDataFactory {

	/**
	 * @var \Shopsys\ShopBundle\Model\Payment\PaymentEditFacade
	 */
	private $paymentEditFacade;

	/**
	 * @var \Shopsys\ShopBundle\Model\Payment\PaymentPriceEditFacade
	 */
	private $paymentPriceEditFacade;

	/**
	 * @var \Shopsys\ShopBundle\Model\Pricing\Vat\VatFacade
	 */
	private $vatFacade;

	public function __construct(
		PaymentEditFacade $paymentEditFacade,
		PaymentPriceEditFacade $paymentPriceEditFacade,
		VatFacade $vatFacade
	) {
		$this->paymentEditFacade = $paymentEditFacade;
		$this->paymentPriceEditFacade = $paymentPriceEditFacade;
		$this->vatFacade = $vatFacade;
	}

	/**
	 * @return \Shopsys\ShopBundle\Model\Payment\PaymentEditData
	 */
	public function createDefault() {
		$paymentEditData = new PaymentEditData();
		$paymentEditData->paymentData->vat = $this->vatFacade->getDefaultVat();

		return $paymentEditData;
	}

	/**
	 * @param \Shopsys\ShopBundle\Model\Payment\Payment $payment
	 * @return \Shopsys\ShopBundle\Model\Payment\PaymentEditData
	 */
	public function createFromPayment(Payment $payment) {
		$paymentEditData = new PaymentEditData();
		$paymentData = new PaymentData();
		$paymentData->setFromEntity($payment, $this->paymentEditFacade->getPaymentDomainsByPayment($payment));
		$paymentEditData->paymentData = $paymentData;

		foreach ($this->paymentPriceEditFacade->getAllByPayment($payment) as $paymentPrice) {
			$paymentEditData->prices[$paymentPrice->getCurrency()->getId()] = $paymentPrice->getPrice();
		}

		return $paymentEditData;
	}
}
