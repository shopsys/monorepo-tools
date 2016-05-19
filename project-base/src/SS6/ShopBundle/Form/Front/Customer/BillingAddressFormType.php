<?php

namespace SS6\ShopBundle\Form\Front\Customer;

use SS6\ShopBundle\Form\FormType;
use SS6\ShopBundle\Form\ValidationGroup;
use SS6\ShopBundle\Model\Customer\BillingAddressData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class BillingAddressFormType extends AbstractType {

	const VALIDATION_GROUP_COMPANY_CUSTOMER = 'companyCustomer';

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
		return 'billing_address_form';
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('telephone', FormType::TEXT, [
				'required' => false,
				'constraints' => [
					new Constraints\Length(['max' => 30, 'maxMessage' => 'Telefon nesmí být delší než {{ limit }} znaků']),
				],
			])
			->add('companyCustomer', FormType::CHECKBOX, ['required' => false])
			->add('companyName', FormType::TEXT, [
				'required' => true,
				'constraints' => [
					new Constraints\NotBlank([
						'message' => 'Vyplňte prosím název firmy',
						'groups' => [self::VALIDATION_GROUP_COMPANY_CUSTOMER],
					]),
					new Constraints\Length([
						'max' => 100,
						'maxMessage' => 'Název firmy nesmí být delší než {{ limit }} znaků',
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
					new Constraints\Length([
						'max' => 50,
						'maxMessage' => 'IČ nesmí být delší než {{ limit }} znaků',
						'groups' => [self::VALIDATION_GROUP_COMPANY_CUSTOMER],
					]),
				],
			])
			->add('companyTaxNumber', FormType::TEXT, [
				'required' => false,
				'constraints' => [
					new Constraints\Length([
						'max' => 50,
						'maxMessage' => 'DIČ nesmí být delší než {{ limit }} znaků',
						'groups' => [self::VALIDATION_GROUP_COMPANY_CUSTOMER],
					]),
				],
			])
			->add('street', FormType::TEXT, [
				'required' => false,
				'constraints' => [
					new Constraints\Length(['max' => 100, 'maxMessage' => 'Název ulice nesmí být delší než {{ limit }} znaků']),
				],
			])
			->add('city', FormType::TEXT, [
				'required' => false,
				'constraints' => [
					new Constraints\Length(['max' => 100, 'maxMessage' => 'Název města nesmí být delší než {{ limit }} znaků']),
				],
			])
			->add('postcode', FormType::TEXT, [
				'required' => false,
				'constraints' => [
					new Constraints\Length(['max' => 30, 'maxMessage' => 'PSČ nesmí být delší než {{ limit }} znaků']),
				],
			])
			->add('country', FormType::CHOICE, [
				'required' => false,
				'choice_list' => new ObjectChoiceList($this->countries, 'name', [], null, 'id'),
			]);
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
