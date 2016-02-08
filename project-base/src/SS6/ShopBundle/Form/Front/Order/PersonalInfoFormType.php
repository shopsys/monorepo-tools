<?php

namespace SS6\ShopBundle\Form\Front\Order;

use SS6\ShopBundle\Component\Transformers\InverseTransformer;
use SS6\ShopBundle\Form\FormType;
use SS6\ShopBundle\Form\ValidationGroup;
use SS6\ShopBundle\Model\Order\FrontOrderData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class PersonalInfoFormType extends AbstractType {

	const VALIDATION_GROUP_COMPANY_CUSTOMER = 'companyCustomer';
	const VALIDATION_GROUP_DIFFERENT_DELIVERY_ADDRESS = 'differentDeliveryAddress';

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
				],
			])
			->add('lastName', FormType::TEXT, [
				'constraints' => [
					new Constraints\NotBlank(['message' => 'Vyplňte prosím příjmení']),
				],
			])
			->add('email', FormType::EMAIL, [
				'constraints' => [
					new Constraints\NotBlank(['message' => 'Vyplňte prosím e-mail']),
					new Constraints\Email(['message' => 'Vyplňte prosím platný e-mail']),
				],
			])
			->add('telephone', FormType::TEXT, [
				'constraints' => [
					new Constraints\NotBlank(['message' => 'Vyplňte prosím telefon']),
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
			->add('street', FormType::TEXT, [
				'constraints' => [
					new Constraints\NotBlank(['message' => 'Vyplňte prosím ulici']),
				],
			])
			->add('city', FormType::TEXT, [
				'constraints' => [
					new Constraints\NotBlank(['message' => 'Vyplňte prosím město']),
				],
			])
			->add('postcode', FormType::TEXT, [
				'constraints' => [
					new Constraints\NotBlank(['message' => 'Vyplňte prosím PSČ']),
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
				],
			])
			->add('deliveryCompanyName', FormType::TEXT, ['required' => false])
			->add('deliveryTelephone', FormType::TEXT, ['required' => false])
			->add('deliveryStreet', FormType::TEXT, [
				'required' => true,
				'constraints' => [
					new Constraints\NotBlank([
						'message' => 'Vyplňte prosím ulici',
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
				],
			])
			->add('deliveryPostcode', FormType::TEXT, [
				'required' => true,
				'constraints' => [
					new Constraints\NotBlank([
						'message' => 'Vyplňte prosím PSČ',
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
