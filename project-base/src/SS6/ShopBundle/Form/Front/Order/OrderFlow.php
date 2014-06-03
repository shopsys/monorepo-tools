<?php

namespace SS6\ShopBundle\Form\Front\Order;

use Craue\FormFlowBundle\Form\FormFlow;
use SS6\ShopBundle\Form\Front\Order\TransportAndPaymentFormType;
use SS6\ShopBundle\Form\Front\Order\PersonalInfoFormType;

class OrderFlow extends FormFlow {
	/**
	 * @var bool
	 */
	protected $allowDynamicStepNavigation = true;

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

	public function getFormOptions($step, array $options = array()) {
		$options = parent::getFormOptions($step, $options);

		// Remove default validation_groups by step.
		// Otherwise FormFactory uses is instead of FormType's callback.
		if (isset($options['validation_groups'])) {
			unset($options['validation_groups']);
		}

		return $options;
	}

	public function saveSentStepData() {
		$stepData = $this->retrieveStepData();

		foreach ($this->getSteps() as $step) {
			$stepForm = $this->createFormForStep($step->getNumber());
			if ($this->getRequest()->request->has($stepForm->getName())) {
				$stepData[$step->getNumber()] = $this->getRequest()->request->get($stepForm->getName());
			}
		}

		$this->saveStepData($stepData);
	}

	public function isBackToCartTransition() {
		return $this->getRequestedStepNumber() === 2
			&& $this->getRequestedTransition() === self::TRANSITION_BACK;
	}

}
