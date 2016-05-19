<?php

namespace SS6\ShopBundle\Form\Front\Customer;

use SS6\ShopBundle\Form\FormType;
use SS6\ShopBundle\Form\Front\Customer\UserFormType;
use SS6\ShopBundle\Model\Customer\CustomerData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CustomerFormType extends AbstractType {

	/**
	 * @var \SS6\ShopBundle\Model\Country\Country[]|null
	 */
	private $countries;

	/**
	 * @param \SS6\ShopBundle\Model\Country\Country[]|null $countries
	 */
	public function __construct(array $countries = null) {
		$this->countries = $countries;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'customer_form';
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('userData', new UserFormType())
			->add('billingAddressData', new BillingAddressFormType($this->countries))
			->add('deliveryAddressData', new DeliveryAddressFormType($this->countries))
			->add('save', FormType::SUBMIT);
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'data_class' => CustomerData::class,
			'attr' => ['novalidate' => 'novalidate'],
		]);
	}

}
