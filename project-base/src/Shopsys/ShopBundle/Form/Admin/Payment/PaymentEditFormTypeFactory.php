<?php

namespace SS6\ShopBundle\Form\Admin\Payment;

use SS6\ShopBundle\Form\Admin\Payment\PaymentFormTypeFactory;
use SS6\ShopBundle\Model\Pricing\Currency\CurrencyFacade;

class PaymentEditFormTypeFactory {

	/**
	 * @var \SS6\ShopBundle\Form\Admin\Payment\PaymentFormTypeFactory
	 */
	private $paymentFormTypeFactory;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Currency\CurrencyFacade
	 */
	private $currencyFacade;

	public function __construct(PaymentFormTypeFactory $paymentFormTypeFactory, CurrencyFacade $currencyFacade) {
		$this->paymentFormTypeFactory = $paymentFormTypeFactory;
		$this->currencyFacade = $currencyFacade;
	}

	/**
	 * @return \SS6\ShopBundle\Form\Admin\Payment\PaymentFormType
	 */
	public function create() {
		$currencies = $this->currencyFacade->getAll();

		return new PaymentEditFormType($this->paymentFormTypeFactory, $currencies);
	}

}
