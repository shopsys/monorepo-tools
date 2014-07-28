<?php

namespace SS6\ShopBundle\Form\Admin\Customer;

use SS6\ShopBundle\Model\Customer\CustomerFormData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

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
	 *
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('user', new UserFormType($this->scenario))
			->add('telephone', 'text', array('required' => false))
			->add('companyCustomer', 'checkbox', array('required' => false))
			->add('companyName', 'text', array(
				'required' => true,
				'constraints' => array(
					new Constraints\NotBlank(array(
						'message' => 'Vyplňte prosím název firmy',
						'groups' => array('companyCustomer'),
					)),
				),
			))
			->add('companyNumber', 'text', array(
				'required' => true,
				'constraints' => array(
					new Constraints\NotBlank(array(
						'message' => 'Vyplňte prosím IČ',
						'groups' => array('companyCustomer'),
					)),
				),
			))
			->add('companyTaxNumber', 'text', array('required' => false))
			->add('street', 'text', array('required' => false))
			->add('city', 'text', array('required' => false))
			->add('postcode', 'text', array('required' => false))
			->add('country', 'text', array('required' => false))
			->add('deliveryAddressFilled', 'checkbox', array('required' => false))
			->add('deliveryCompanyName', 'text', array('required' => false))
			->add('deliveryContactPerson', 'text', array('required' => false))
			->add('deliveryTelephone', 'text', array('required' => false))
			->add('deliveryStreet', 'text', array(
				'required' => true,
				'constraints' => array(
					new Constraints\NotBlank(array(
						'message' => 'Vyplňte prosím ulici',
						'groups' => array('differentDeliveryAddress'),
					)),
				),
			))
			->add('deliveryCity', 'text', array(
				'required' => true,
				'constraints' => array(
					new Constraints\NotBlank(array(
						'message' => 'Vyplňte prosím město',
						'groups' => array('differentDeliveryAddress'),
					)),
				),
			))
			->add('deliveryPostcode', 'text', array(
				'required' => true,
				'constraints' => array(
					new Constraints\NotBlank(array(
						'message' => 'Vyplňte prosím PSČ',
						'groups' => array('differentDeliveryAddress'),
					)),
				),
			))
			->add('deliveryCountry', 'text', array(
				'required' => true,
				'constraints' => array(
					new Constraints\NotBlank(array(
						'message' => 'Vyplňte prosím stát',
						'groups' => array('differentDeliveryAddress'),
					)),
				),
			))
			->add('save', 'submit');
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
			'data_class' => CustomerFormData::class,
			'attr' => array('novalidate' => 'novalidate'),
			'validation_groups' => function(FormInterface $form) {
				$validationGroups = array('Default');

				$customerFormData = $form->getData();
				/* @var $customerFormData \SS6\ShopBundle\Model\Customer\CustomerFormData */

				if ($customerFormData->getCompanyCustomer()) {
					$validationGroups[] = 'companyCustomer';
				}
				if ($customerFormData->getDeliveryAddressFilled()) {
					$validationGroups[] = 'differentDeliveryAddress';
				}

				return $validationGroups;
			},
		));
	}

}
