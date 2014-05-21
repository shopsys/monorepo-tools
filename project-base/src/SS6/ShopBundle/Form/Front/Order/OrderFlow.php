<?php

namespace SS6\ShopBundle\Form\Front\Order;

use Craue\FormFlowBundle\Form\FormFlow;
use SS6\ShopBundle\Form\Front\Order\TransportAndPaymentFormType;
use SS6\ShopBundle\Form\Front\Order\PersonalInfoFormType;

class OrderFlow extends FormFlow {
	/**
	 * @var \SS6\ShopBundle\Model\Transport\Transport[]
	 */
	private $transports;

	/**
	 * @var \SS6\ShopBundle\Model\Payment\Payment[]
	 */
	private $payments;

	/**
	 * @param \SS6\ShopBundle\Model\Transport\Transport[] $transports
	 * @param \SS6\ShopBundle\Model\Payment\Payment[] $payments
	 */
	public function setFormTypesData(array $transports, array $payments) {
		$this->transports = $transports;
		$this->payments = $payments;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'order';
	}

	/**
	 * @return array
	 */
	protected function loadStepsConfig() {
		return array(
			array(
				'skip' => true, // the 1st step is the shopping cart
			),
			array(
				'type' => new TransportAndPaymentFormType($this->transports, $this->payments),
			),
			array(
				'type' => new PersonalInfoFormType(),
			),
		);
	}

}
