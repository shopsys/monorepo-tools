<?php

namespace SS6\ShopBundle\Form\Front\Order;

use SS6\ShopBundle\Form\SingleCheckboxChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceList;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PaymentFromType extends AbstractType {
	/**
	 * @var \SS6\ShopBundle\Model\Payment\Payment[]
	 */
	private $payments;

	/**
	 * @param \SS6\ShopBundle\Model\Payment\Payment[] $payments
	 */
	public function __construct(array $payments) {
		$this->payments = $payments;
	}

	public function getParent() {
		return new SingleCheckboxChoiceType();
	}

	public function getName() {
		return 'payment';
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$paymentChoices = array();
		foreach ($this->payments as $payment) {
			/* @var $payment \SS6\ShopBundle\Model\Payment\Payment */
			$paymentChoices[$payment->getId()] = $payment;
		}

		$resolver->setDefaults(array(
			'choice_list' => $this->getChoiceList(),
		));
	}

	private function getChoiceList() {
		$labels = array();
		foreach ($this->payments as $payment) {
			$labels[] = $payment->getName();
		}

		return new ChoiceList($this->payments, $labels);
	}

}
