<?php

namespace Shopsys\ShopBundle\Form\Admin\Order;

use Shopsys\ShopBundle\Component\Domain\SelectedDomain;
use Shopsys\ShopBundle\Form\Admin\Order\OrderFormType;
use Shopsys\ShopBundle\Model\Country\CountryFacade;
use Shopsys\ShopBundle\Model\Order\Order;
use Shopsys\ShopBundle\Model\Order\Status\OrderStatusFacade;
use Shopsys\ShopBundle\Model\Payment\PaymentEditFacade;
use Shopsys\ShopBundle\Model\Transport\TransportEditFacade;

class OrderFormTypeFactory {

	/**
	 * @var \Shopsys\ShopBundle\Model\Order\Status\OrderStatusFacade
	 */
	private $orderStatusFacade;

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
	 * @var \Shopsys\ShopBundle\Component\Domain\SelectedDomain
	 */
	private $selectedDomain;

	public function __construct(
		OrderStatusFacade $orderStatusFacade,
		TransportEditFacade $transportEditFacade,
		PaymentEditFacade $paymentEditFacade,
		CountryFacade $countryFacade,
		SelectedDomain $selectedDomain
	) {
		$this->orderStatusFacade = $orderStatusFacade;
		$this->transportEditFacade = $transportEditFacade;
		$this->paymentEditFacade = $paymentEditFacade;
		$this->countryFacade = $countryFacade;
		$this->selectedDomain = $selectedDomain;
	}

	/**
	 * @param \Shopsys\ShopBundle\Model\Order\Order $order
	 * @return \Shopsys\ShopBundle\Form\Admin\Order\OrderFormType
	 */
	public function createForOrder(Order $order) {
		$orderDomainId = $order->getDomainId();
		$payments = $this->paymentEditFacade->getVisibleByDomainId($orderDomainId);
		$transports = $this->transportEditFacade->getVisibleByDomainId($orderDomainId, $payments);
		$countries = $this->countryFacade->getAllByDomainId($this->selectedDomain->getId());

		if (!in_array($order->getPayment(), $payments, true)) {
			$payments[] = $order->getPayment();
		}
		if (!in_array($order->getTransport(), $transports, true)) {
			$transports[] = $order->getTransport();
		}

		return new OrderFormType(
			$this->orderStatusFacade->getAll(),
			$transports,
			$payments,
			$countries
		);
	}
}
