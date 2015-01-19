<?php

namespace SS6\ShopBundle\Form\Admin\Customer;

use SS6\ShopBundle\Model\Customer\BillingAddressData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class BillingAddressFormType extends AbstractType {

	/**
	 * @return string
	 */
	public function getName() {
		return 'billingAddress';
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
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
			->add('postcode', 'text', array('required' => false));
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
			'data_class' => BillingAddressData::class,
			'attr' => array('novalidate' => 'novalidate'),
			'validation_groups' => function (FormInterface $form) {
				$validationGroups = array('Default');

				$customerData = $form->getData();
				/* @var $customerData \SS6\ShopBundle\Model\Customer\CustomerData */

				if ($customerData->companyCustomer) {
					$validationGroups[] = 'companyCustomer';
				}

				return $validationGroups;
			},
		));
	}

}
