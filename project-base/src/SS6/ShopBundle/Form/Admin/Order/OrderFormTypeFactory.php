<?php

namespace SS6\ShopBundle\Form\Admin\Order;

use SS6\ShopBundle\Form\Admin\Order\OrderFormType;
use SS6\ShopBundle\Model\Order\Status\OrderStatusFacade;

class OrderFormTypeFactory {

	/**
	 * @var \SS6\ShopBundle\Model\Order\Status\OrderStatusFacade
	 */
	private $orderStatusFacade;

	public function __construct(OrderStatusFacade $orderStatusFacade) {
		$this->orderStatusFacade = $orderStatusFacade;
	}

	/**
	 * @return \SS6\ShopBundle\Form\Admin\Order\OrderFormType
	 */
	public function create() {
		return new OrderFormType($this->orderStatusFacade->getAll());
	}
}
