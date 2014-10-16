<?php

namespace SS6\ShopBundle\Model\Order;

use SS6\ShopBundle\Form\Front\Order\OrderFlow;
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
	 * @param \SS6\ShopBundle\Form\Front\Order\OrderFlow $orderFlow
	 * @param \SS6\ShopBundle\Model\Order\PaymentEditFacade $paymentEditFacade
	 * @param \SS6\ShopBundle\Model\Transport\TransportEditFacade $transportEditFacade
	 */
	public function __construct(
		OrderFlow $orderFlow,
		PaymentEditFacade $paymentEditFacade,
		TransportEditFacade $transportEditFacade
	) {
		$this->orderFlow = $orderFlow;
		$this->paymentEditFacade = $paymentEditFacade;
		$this->transportEditFacade = $transportEditFacade;
	}

	public function resetOrderForm() {
		$payments = $this->paymentEditFacade->getVisibleOnCurrentDomain();
		$transports = $this->transportEditFacade->getVisible($payments);
		$this->orderFlow->setFormTypesData($transports, $payments);
		$this->orderFlow->reset();
	}

}
