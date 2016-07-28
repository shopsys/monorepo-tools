<?php

namespace SS6\ShopBundle\Form\Front\Order;

use SS6\ShopBundle\Component\Transformers\InverseTransformer;
use SS6\ShopBundle\Form\FormType;
use SS6\ShopBundle\Form\ValidationGroup;
use SS6\ShopBundle\Model\Country\Country;
use SS6\ShopBundle\Model\Order\FrontOrderData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class PersonalInfoFormType extends AbstractType {

	const VALIDATION_GROUP_COMPANY_CUSTOMER = 'companyCustomer';
	const VALIDATION_GROUP_DIFFERENT_DELIVERY_ADDRESS = 'differentDeliveryAddress';

	/**
	 * @var \SS6\ShopBundle\Model\Country\Country[]
	 */
	private $countries;

	/**
	 * @param \SS6\ShopBundle\Model\Country\Country[] $countries
	 */
	public function __construct(array $countries) {
		$this->countries = $countries;
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('firstName', FormType::TEXT, [
				'constraints' => [
					new Constraints\NotBlank(['message' => 'Vyplňte prosím jméno']),
					new Constraints\Length(['max' => 100, 'maxMessage' => 'Jméno nesmí být delší než {{ limit }} znaků']),
				],
			])
			->add('lastName', FormType::TEXT, [
				'constraints' => [
					new Constraints\NotBlank(['message' => 'Vyplňte prosím příjmení']),
					new Constraints\Length(['max' => 100, 'maxMessage' => 'Příjmení nesmí být delší než {{ limit }} znaků']),
				],
			])
			->add('email', FormType::EMAIL, [
				'constraints' => [
					new Constraints\NotBlank(['message' => 'Vyplňte prosím e-mail']),
					new Constraints\Email(['message' => 'Vyplňte prosím platný e-mail']),
					new Constraints\Length(['max' => 255, 'maxMessage' => 'E-mail nesmí být delší než {{ limit }} znaků']),
				],
			])
			->add('telephone', FormType::TEXT, [
				'constraints' => [
					new Constraints\NotBlank(['message' => 'Vyplňte prosím telefon']),
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
					new Constraints\Length(['max' => 100,
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
				'constraints' => [
					new Constraints\NotBlank(['message' => 'Vyplňte prosím ulici']),
					new Constraints\Length(['max' => 100, 'maxMessage' => 'Název ulice nesmí být delší než {{ limit }} znaků']),
				],
			])
			->add('city', FormType::TEXT, [
				'constraints' => [
					new Constraints\NotBlank(['message' => 'Vyplňte prosím město']),
					new Constraints\Length(['max' => 100, 'maxMessage' => 'Název města nesmí být delší než {{ limit }} znaků']),
				],
			])
			->add('postcode', FormType::TEXT, [
				'constraints' => [
					new Constraints\NotBlank(['message' => 'Vyplňte prosím PSČ']),
					new Constraints\Length(['max' => 30, 'maxMessage' => 'PSČ nesmí být delší než {{ limit }} znaků']),
				],
			])
			->add('country', FormType::CHOICE, [
				'choice_list' => new ObjectChoiceList($this->countries, 'name', [], null, 'id'),
				'constraints' => [
					new Constraints\NotBlank(['message' => 'Prosím vyberte stát']),
				],
			])
			->add($builder
				->create('deliveryAddressFilled', FormType::CHECKBOX, [
					'required' => false,
					'property_path' => 'deliveryAddressSameAsBillingAddress',
				])
				->addModelTransformer(new InverseTransformer())
			)
			->add('deliveryContactPerson', FormType::TEXT, [
				'required' => true,
				'constraints' => [
					new Constraints\NotBlank([
						'message' => 'Vyplňte prosím kontatkní osobu',
						'groups' => [self::VALIDATION_GROUP_DIFFERENT_DELIVERY_ADDRESS],
					]),
					new Constraints\Length([
						'max' => 200,
						'maxMessage' => 'Jméno kontaktní osoby nesmí být delší než {{ limit }} znaků',
						'groups' => [self::VALIDATION_GROUP_DIFFERENT_DELIVERY_ADDRESS],
					]),
				],
			])
			->add('deliveryCompanyName', FormType::TEXT, [
				'required' => false,
				'constraints' => [
					new Constraints\Length([
						'max' => 100,
						'maxMessage' => 'Název společnosti nesmí být delší než {{ limit }} znaků',
						'groups' => [self::VALIDATION_GROUP_DIFFERENT_DELIVERY_ADDRESS],
					]),
				],
			])
			->add('deliveryTelephone', FormType::TEXT, [
				'required' => false,
				'constraints' => [
					new Constraints\Length([
						'max' => 30,
						'maxMessage' => 'Telefon nesmí být delší než {{ limit }} znaků',
						'groups' => [self::VALIDATION_GROUP_DIFFERENT_DELIVERY_ADDRESS],
					]),
				],
			])
			->add('deliveryStreet', FormType::TEXT, [
				'required' => true,
				'constraints' => [
					new Constraints\NotBlank([
						'message' => 'Vyplňte prosím ulici',
						'groups' => [self::VALIDATION_GROUP_DIFFERENT_DELIVERY_ADDRESS],
					]),
					new Constraints\Length([
						'max' => 100,
						'maxMessage' => 'Název ulice nesmí být delší než {{ limit }} znaků',
						'groups' => [self::VALIDATION_GROUP_DIFFERENT_DELIVERY_ADDRESS],
					]),
				],
			])
			->add('deliveryCity', FormType::TEXT, [
				'required' => true,
				'constraints' => [
					new Constraints\NotBlank([
						'message' => 'Vyplňte prosím město',
						'groups' => [self::VALIDATION_GROUP_DIFFERENT_DELIVERY_ADDRESS],
					]),
					new Constraints\Length([
						'max' => 100,
						'maxMessage' => 'Název města nesmí být delší než {{ limit }} znaků',
						'groups' => [self::VALIDATION_GROUP_DIFFERENT_DELIVERY_ADDRESS],
					]),
				],
			])
			->add('deliveryPostcode', FormType::TEXT, [
				'required' => true,
				'constraints' => [
					new Constraints\NotBlank([
						'message' => 'Vyplňte prosím PSČ',
						'groups' => [self::VALIDATION_GROUP_DIFFERENT_DELIVERY_ADDRESS],
					]),
					new Constraints\Length([
						'max' => 30,
						'maxMessage' => 'PSČ nesmí být delší než {{ limit }} znaků',
						'groups' => [self::VALIDATION_GROUP_DIFFERENT_DELIVERY_ADDRESS],
					]),
				],
			])
			->add('deliveryCountry', FormType::CHOICE, [
				'required' => true,
				'choice_list' => new ObjectChoiceList($this->countries, 'name', [], null, 'id'),
				'constraints' => [
					new Constraints\NotBlank([
						'message' => 'Prosím vyberte stát',
						'groups' => [self::VALIDATION_GROUP_DIFFERENT_DELIVERY_ADDRESS],
					]),
				],
			])
			->add('note', FormType::TEXTAREA, ['required' => false])
			->add('termsAndConditionsAgreement', FormType::CHECKBOX, [
				'required' => true,
				'mapped' => false,
				'constraints' => [
					new Constraints\NotBlank([
						'message' => 'Musíte souhlasit s obchodními podmínkami.',
					]),
				],
			])
			->add('newsletterSubscription', FormType::CHECKBOX, [
				'required' => false,
			])
			->add('save', FormType::SUBMIT);
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'order_personal_info_form';
	}

	/**
	 * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'data_class' => FrontOrderData::class,
			'attr' => ['novalidate' => 'novalidate'],
			'validation_groups' => function (FormInterface $form) {
				$validationGroups = [ValidationGroup::VALIDATION_GROUP_DEFAULT];

				$orderData = $form->getData();
				/* @var $data \SS6\ShopBundle\Model\Order\OrderData */

				if ($orderData->companyCustomer) {
					$validationGroups[] = self::VALIDATION_GROUP_COMPANY_CUSTOMER;
				}
				if (!$orderData->deliveryAddressSameAsBillingAddress) {
					$validationGroups[] = self::VALIDATION_GROUP_DIFFERENT_DELIVERY_ADDRESS;
				}

				return $validationGroups;
			},
		]);
	}

}
