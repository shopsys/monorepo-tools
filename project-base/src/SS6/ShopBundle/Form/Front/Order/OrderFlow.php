<?php

namespace SS6\ShopBundle\Form\Front\Order;

use Craue\FormFlowBundle\Form\FormFlow;
use SS6\ShopBundle\Form\Front\Order\TransportAndPaymentFormType;

class OrderFlow extends FormFlow {
	/**
	 * @var \SS6\ShopBundle\Model\Transport\Transport[]
	 */
	private $transports;

	/**
	 * @var \SS6\ShopBundle\Model\Payment\Payment[]
	 */
	private $payments;

	public function setFormTypesData(array $transports, array $payments) {
		$this->transports = $transports;
		$this->payments = $payments;
	}

	public function getName() {
		return 'order';
	}

	protected function loadStepsConfig() {
		return array(
			array(
				'label' => 'step1',
				'type' => new TransportAndPaymentFormType($this->transports, $this->payments),
			),
		);
	}

}
