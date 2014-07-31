<?php

namespace SS6\ShopBundle\Form\Admin\Customer;

use SS6\ShopBundle\Model\Customer\CustomerData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CustomerFormType extends AbstractType {

	const SCENARIO_CREATE = 'create';
	const SCENARIO_EDIT = 'edit';

	private $scenario;

	/**
	 * @param string $scenario
	 */
	public function __construct($scenario) {
		$this->scenario = $scenario;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'customer';
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('userData', new UserFormType($this->scenario))
			->add('billingAddressData', new BillingAddressFormType())
			->add('deliveryAddressData', new DeliveryAddressFormType())
			->add('save', 'submit');
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
			'data_class' => CustomerData::class,
			'attr' => array('novalidate' => 'novalidate'),
		));
	}

}
