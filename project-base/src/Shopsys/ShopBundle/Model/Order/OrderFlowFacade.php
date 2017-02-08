<?php

namespace Shopsys\ShopBundle\Model\Order;

use Shopsys\ShopBundle\Form\Front\Order\OrderFlow;
use Shopsys\ShopBundle\Model\Country\CountryFacade;
use Shopsys\ShopBundle\Model\Payment\PaymentEditFacade;
use Shopsys\ShopBundle\Model\Transport\TransportEditFacade;

class OrderFlowFacade {

	/**
	 * @var \Shopsys\ShopBundle\Form\Front\Order\OrderFlow
	 */
	private $orderFlow;

	/**
	 * @var \Shopsys\ShopBundle\Model\Transport\TransportEditFacade
	 */
	private $transportEditFacade;

	/**
	 * @var \Shopsys\ShopBundle\Model\Payment\PaymentEditFacade
	 */
	private $paymentEditFacade;

	/**
	 * @var \Shopsys\ShopBundle\Model\Country\CountryFacade
	 */
	private $countryFacade;

	/**
	 * @param \Shopsys\ShopBundle\Form\Front\Order\OrderFlow $orderFlow
	 * @param \Shopsys\ShopBundle\Model\Payment\PaymentEditFacade $paymentEditFacade
	 * @param \Shopsys\ShopBundle\Model\Transport\TransportEditFacade $transportEditFacade
	 * @param \Shopsys\ShopBundle\Model\Country\CountryFacade $countryFacade
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
