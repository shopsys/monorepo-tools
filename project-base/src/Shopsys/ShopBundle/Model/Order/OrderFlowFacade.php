<?php

namespace SS6\ShopBundle\Model\Order;

use SS6\ShopBundle\Form\Front\Order\OrderFlow;
use SS6\ShopBundle\Model\Country\CountryFacade;
use SS6\ShopBundle\Model\Payment\PaymentEditFacade;
use SS6\ShopBundle\Model\Transport\TransportEditFacade;

class OrderFlowFacade {

	/**
	 * @var \SS6\ShopBundle\Form\Front\Order\OrderFlow
	 */
	private $orderFlow;

	/**
	 * @var \SS6\ShopBundle\Model\Transport\TransportEditFacade
	 */
	private $transportEditFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Payment\PaymentEditFacade
	 */
	private $paymentEditFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Country\CountryFacade
	 */
	private $countryFacade;

	/**
	 * @param \SS6\ShopBundle\Form\Front\Order\OrderFlow $orderFlow
	 * @param \SS6\ShopBundle\Model\Payment\PaymentEditFacade $paymentEditFacade
	 * @param \SS6\ShopBundle\Model\Transport\TransportEditFacade $transportEditFacade
	 * @param \SS6\ShopBundle\Model\Country\CountryFacade $countryFacade
	 */
	public function __construct(
		OrderFlow $orderFlow,
		PaymentEditFacade $paymentEditFacade,
		TransportEditFacade $transportEditFacade,
		CountryFacade $countryFacade
	) {
		$this->orderFlow = $orderFlow;
		$this->paymentEditFacade = $paymentEditFacade;
		$this->transportEditFacade = $transportEditFacade;
		$this->countryFacade = $countryFacade;
	}

	public function resetOrderForm() {
		$payments = $this->paymentEditFacade->getVisibleOnCurrentDomain();
		$transports = $this->transportEditFacade->getVisibleOnCurrentDomain($payments);
		$countries = $this->countryFacade->getAllOnCurrentDomain();
		$this->orderFlow->setFormTypesData($transports, $payments, $countries);
		$this->orderFlow->reset();
	}

}
