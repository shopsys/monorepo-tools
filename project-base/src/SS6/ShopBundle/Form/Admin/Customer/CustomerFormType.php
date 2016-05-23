<?php

namespace SS6\ShopBundle\Form\Admin\Customer;

use SS6\ShopBundle\Form\FormType;
use SS6\ShopBundle\Model\Customer\CustomerData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CustomerFormType extends AbstractType {

	const SCENARIO_CREATE = 'create';
	const SCENARIO_EDIT = 'edit';

	/**
	 * @var string
	 */
	private $scenario;

	/**
	 * @var \SS6\ShopBundle\Component\Domain\SelectedDomain
	 */
	private $selectedDomain;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Group\PricingGroup[]
	 */
	private $pricingGroups;

	/**
	 * @var \SS6\ShopBundle\Model\Country\Country[]
	 */
	private $countries;

	/**
	 * @param string $scenario
	 * @param \SS6\ShopBundle\Model\Country\Country[] $countries
	 * @param \SS6\ShopBundle\Component\Domain\SelectedDomain $selectedDomain
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup[]|null $pricingGroups
	 */
	public function __construct($scenario, array $countries, $selectedDomain = null, $pricingGroups = null) {
		$this->scenario = $scenario;
		$this->countries = $countries;
		$this->selectedDomain = $selectedDomain;
		$this->pricingGroups = $pricingGroups;
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
			->add('userData', new UserFormType($this->scenario, $this->selectedDomain, $this->pricingGroups))
			->add('billingAddressData', new BillingAddressFormType($this->countries))
			->add('deliveryAddressData', new DeliveryAddressFormType($this->countries))
			->add('save', FormType::SUBMIT);

		if ($this->scenario === self::SCENARIO_CREATE) {
			$builder->add('sendRegistrationMail', FormType::CHECKBOX, ['required' => false]);
		}
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'data_class' => CustomerData::class,
			'attr' => ['novalidate' => 'novalidate'],
		]);
	}

}
