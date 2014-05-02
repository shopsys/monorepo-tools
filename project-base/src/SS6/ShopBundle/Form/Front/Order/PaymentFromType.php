<?php

namespace SS6\ShopBundle\Form\Front\Order;

use SS6\ShopBundle\Form\SingleCheckboxChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
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

	/**
	 * @return \SS6\ShopBundle\Form\SingleCheckboxChoiceType
	 */
	public function getParent() {
		return new SingleCheckboxChoiceType();
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'payment';
	}

	/**
	 * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
			'choice_list' => new ObjectChoiceList($this->payments, 'name', array(), null, 'id'),
		));
	}

}
