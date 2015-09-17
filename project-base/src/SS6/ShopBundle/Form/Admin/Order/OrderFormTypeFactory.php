<?php

namespace SS6\ShopBundle\Form\Admin\Order;

use SS6\ShopBundle\Form\Admin\Order\OrderFormType;
use SS6\ShopBundle\Model\Order\Order;
use SS6\ShopBundle\Model\Order\Status\OrderStatusFacade;
use SS6\ShopBundle\Model\Payment\PaymentEditFacade;
use SS6\ShopBundle\Model\Transport\TransportEditFacade;

class OrderFormTypeFactory {

	/**
	 * @var \SS6\ShopBundle\Model\Order\Status\OrderStatusFacade
	 */
	private $orderStatusFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Transport\TransportEditFacade
	 */
	private $transportEditFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Payment\PaymentEditFacade
	 */
	private $paymentEditFacade;

	public function __construct(
		OrderStatusFacade $orderStatusFacade,
		TransportEditFacade $transportEditFacade,
		PaymentEditFacade $paymentEditFacade
	) {
		$this->orderStatusFacade = $orderStatusFacade;
		$this->transportEditFacade = $transportEditFacade;
		$this->paymentEditFacade = $paymentEditFacade;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Order $order
	 * @return \SS6\ShopBundle\Form\Admin\Order\OrderFormType
	 */
	public function createForOrder(Order $order) {
		$orderDomainId = $order->getDomainId();
		$visiblePaymentsOnDomain = $this->paymentEditFacade->getVisibleByDomainId($orderDomainId);
		$visibleTransportsOnDomain = $this->transportEditFacade->getVisibleByDomainId($orderDomainId, $visiblePaymentsOnDomain);

		return new OrderFormType(
			$this->orderStatusFacade->getAll(),
			$visibleTransportsOnDomain,
			$visiblePaymentsOnDomain
		);
	}
}
