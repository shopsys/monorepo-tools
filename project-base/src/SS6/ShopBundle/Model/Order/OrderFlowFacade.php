<?php

namespace SS6\ShopBundle\Model\Order;

use SS6\ShopBundle\Form\Front\Order\OrderFlow;
use SS6\ShopBundle\Model\Payment\PaymentRepository;
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
	 * @var \SS6\ShopBundle\Model\Transport\TransportRepository
	 */
	private $transportRepository;

	/**
	 * @param \SS6\ShopBundle\Form\Front\Order\OrderFlow $orderFlow
	 * @param \SS6\ShopBundle\Model\Payment\PaymentRepository $paymentRepository
	 * @param \SS6\ShopBundle\Model\Transport\TransportRepository $transportRepository
	 */
	public function __construct(
		OrderFlow $orderFlow,
		PaymentRepository $paymentRepository,
		TransportRepository $transportRepository
	) {
		$this->orderFlow = $orderFlow;
		$this->paymentRepository = $paymentRepository;
		$this->transportRepository = $transportRepository;
	}

	public function resetOrderForm() {
		$payments = $this->paymentRepository->getVisible();
		$transports = $this->transportRepository->getVisible($payments);
		$this->orderFlow->setFormTypesData($transports, $payments);
		$this->orderFlow->reset();
	}

}
