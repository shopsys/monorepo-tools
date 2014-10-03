<?php

namespace SS6\ShopBundle\Model\Order;

use SS6\ShopBundle\Form\Front\Order\OrderFlow;
use SS6\ShopBundle\Model\Payment\PaymentRepository;
use SS6\ShopBundle\Model\Transport\TransportEditFacade;
use SS6\ShopBundle\Model\Transport\TransportRepository;

class OrderFlowFacade {

	/**
	 * @var \SS6\ShopBundle\Form\Front\Order\OrderFlow
	 */
	private $orderFlow;

	/**
	 * @var \SS6\ShopBundle\Model\Payment\PaymentRepository
	 */
	private $paymentRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Transport\TransportEditFacade
	 */
	private $transportEditFacade;

	/**
	 * @param \SS6\ShopBundle\Form\Front\Order\OrderFlow $orderFlow
	 * @param \SS6\ShopBundle\Model\Payment\PaymentRepository $paymentRepository
	 * @param \SS6\ShopBundle\Model\Transport\TransportEditFacade $transportEditFacade
	 */
	public function __construct(
		OrderFlow $orderFlow,
		PaymentRepository $paymentRepository,
		TransportEditFacade $transportEditFacade
	) {
		$this->orderFlow = $orderFlow;
		$this->paymentRepository = $paymentRepository;
		$this->transportEditFacade = $transportEditFacade;
	}

	public function resetOrderForm() {
		$payments = $this->paymentRepository->getVisible();
		$transports = $this->transportEditFacade->getVisible($payments);
		$this->orderFlow->setFormTypesData($transports, $payments);
		$this->orderFlow->reset();
	}

}
