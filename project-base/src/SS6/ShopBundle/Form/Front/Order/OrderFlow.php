<?php

namespace SS6\ShopBundle\Form\Front\Order;

use Craue\FormFlowBundle\Form\FormFlow;
use Craue\FormFlowBundle\Form\StepInterface;
use SS6\ShopBundle\Form\Front\Order\PersonalInfoFormType;
use SS6\ShopBundle\Form\Front\Order\TransportAndPaymentFormType;

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
	 * @var \SS6\ShopBundle\Model\Country\Country[]
	 */
	private $countries;

	/**
	 * @param \SS6\ShopBundle\Model\Transport\Transport[] $transports
	 * @param \SS6\ShopBundle\Model\Payment\Payment[] $payments
	 * @param \SS6\ShopBundle\Model\Country\Country[] $countries
	 */
	public function setFormTypesData(array $transports, array $payments, array $countries) {
		$this->transports = $transports;
		$this->payments = $payments;
		$this->countries = $countries;
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
		return [
			[
				'skip' => true, // the 1st step is the shopping cart
			],
			[
				'type' => new TransportAndPaymentFormType($this->transports, $this->payments),
			],
			[
				'type' => new PersonalInfoFormType($this->countries),
			],
		];
	}

	/**
	 * @return string
	 */
	protected function determineInstanceId() {
		// Make instance ID constant as we do not want multiple instances of OrderFlow.
		return $this->getInstanceId();
	}

	/**
	 * @param int $step
	 * @param array $options
	 * @return array
	 */
	public function getFormOptions($step, array $options = []) {
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

	/**
	 * @return bool
	 */
	public function isBackToCartTransition() {
		return $this->getRequestedStepNumber() === 2
			&& $this->getRequestedTransition() === self::TRANSITION_BACK;
	}

	/**
	 * @param mixed $formData
	 */
	public function bind($formData) {
		parent::bind($formData); // load current step number

		$firstInvalidStep = $this->getFirstInvalidStep();
		if ($firstInvalidStep !== null && $this->getCurrentStepNumber() > $firstInvalidStep->getNumber()) {
			$this->changeRequestToStep($firstInvalidStep);
			parent::bind($formData); // load changed step
		}
	}

	/**
	 * @return StepInterface|null
	 */
	private function getFirstInvalidStep() {
		foreach ($this->getSteps() as $step) {
			if (!$this->isStepValid($step)) {
				return $step;
			}
		}

		return null;
	}

	/**
	 * @param \Craue\FormFlowBundle\Form\StepInterface $step
	 * @return bool
	 */
	private function isStepValid(StepInterface $step) {
		$stepNumber = $step->getNumber();
		$stepsData = $this->retrieveStepData();
		if (array_key_exists($stepNumber, $stepsData)) {
			$stepForm = $this->createFormForStep($stepNumber);
			$stepForm->submit($stepsData[$stepNumber]); // the form is validated here
			return $stepForm->isValid();
		}

		return $step->getFormType() === null;
	}

	/**
	 * @param \Craue\FormFlowBundle\Form\StepInterface $step
	 */
	private function changeRequestToStep(StepInterface $step) {
		$stepsData = $this->retrieveStepData();
		if (array_key_exists($step->getNumber(), $stepsData)) {
			$stepData = $stepsData[$step->getNumber()];
		} else {
			$stepData = [];
		}

		$request = $this->getRequest()->request;
		$requestParameters = $request->all();
		$requestParameters['flow_order_step'] = $step->getNumber();
		$requestParameters[$step->getFormType()->getName()] = $stepData;
		$request->replace($requestParameters);
	}

}
