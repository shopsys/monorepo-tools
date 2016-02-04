<?php

namespace SS6\ShopBundle\Form\Front\Customer;

use SS6\ShopBundle\Form\FormType;
use SS6\ShopBundle\Form\ValidationGroup;
use SS6\ShopBundle\Model\Customer\BillingAddressData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class BillingAddressFormType extends AbstractType {

	const VALIDATION_GROUP_COMPANY_CUSTOMER = 'companyCustomer';

	/**
	 * @return string
	 */
	public function getName() {
		return 'billing_address_form';
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('telephone', FormType::TEXT, ['required' => false])
			->add('companyCustomer', FormType::CHECKBOX, ['required' => false])
			->add('companyName', FormType::TEXT, [
				'required' => true,
				'constraints' => [
					new Constraints\NotBlank([
						'message' => 'Vyplňte prosím název firmy',
						'groups' => [self::VALIDATION_GROUP_COMPANY_CUSTOMER],
					]),
				],
			])
			->add('companyNumber', FormType::TEXT, [
				'required' => true,
				'constraints' => [
					new Constraints\NotBlank([
						'message' => 'Vyplňte prosím IČ',
						'groups' => [self::VALIDATION_GROUP_COMPANY_CUSTOMER],
					]),
				],
			])
			->add('companyTaxNumber', FormType::TEXT, ['required' => false])
			->add('street', FormType::TEXT, ['required' => false])
			->add('city', FormType::TEXT, ['required' => false])
			->add('postcode', FormType::TEXT, ['required' => false]);
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'data_class' => BillingAddressData::class,
			'attr' => ['novalidate' => 'novalidate'],
			'validation_groups' => function (FormInterface $form) {
				$validationGroups = [ValidationGroup::VALIDATION_GROUP_DEFAULT];

				$billingAddressData = $form->getData();
				/* @var $billingAddressData \SS6\ShopBundle\Model\Customer\BillingAddressData */

				if ($billingAddressData->companyCustomer) {
					$validationGroups[] = self::VALIDATION_GROUP_COMPANY_CUSTOMER;
				}

				return $validationGroups;
			},
		]);
	}

}
