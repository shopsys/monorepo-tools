<?php

namespace SS6\ShopBundle\Form\Front\Order;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class PersonalInfoFormType extends AbstractType {
	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('firstName', 'text', [
				'constraints' => [
					new Constraints\NotBlank(['message' => 'Vyplňte prosím jméno']),
				],
			])
			->add('lastName', 'text', [
				'constraints' => [
					new Constraints\NotBlank(['message' => 'Vyplňte prosím příjmení']),
				],
			])
			->add('email', 'email', [
				'constraints' => [
					new Constraints\NotBlank(['message' => 'Vyplňte prosím e-mail']),
					new Constraints\Email(['message' => 'Vyplňte prosím platný e-mail']),
				],
			])
			->add('telephone', 'text', [
				'constraints' => [
					new Constraints\NotBlank(['message' => 'Vyplňte prosím telefon']),
				],
			])
			->add('companyCustomer', 'checkbox', ['required' => false])
			->add('companyName', 'text', [
				'required' => true,
				'constraints' => [
					new Constraints\NotBlank([
						'message' => 'Vyplňte prosím název firmy',
						'groups' => ['companyCustomer'],
					]),
				],
			])
			->add('companyNumber', 'text', [
				'required' => true,
				'constraints' => [
					new Constraints\NotBlank([
						'message' => 'Vyplňte prosím IČ',
						'groups' => ['companyCustomer'],
					]),
				],
			])
			->add('companyTaxNumber', 'text', ['required' => false])
			->add('street', 'text', [
				'constraints' => [
					new Constraints\NotBlank(['message' => 'Vyplňte prosím ulici']),
				],
			])
			->add('city', 'text', [
				'constraints' => [
					new Constraints\NotBlank(['message' => 'Vyplňte prosím město']),
				],
			])
			->add('postcode', 'text', [
				'constraints' => [
					new Constraints\NotBlank(['message' => 'Vyplňte prosím PSČ']),
				],
			])
			->add('deliveryAddressFilled', 'checkbox', ['required' => false])
			->add('deliveryContactPerson', 'text', ['required' => false])
			->add('deliveryCompanyName', 'text', ['required' => false])
			->add('deliveryTelephone', 'text', ['required' => false])
			->add('deliveryStreet', 'text', [
				'required' => true,
				'constraints' => [
					new Constraints\NotBlank([
						'message' => 'Vyplňte prosím ulici',
						'groups' => ['differentDeliveryAddress'],
					]),
				],
			])
			->add('deliveryCity', 'text', [
				'required' => true,
				'constraints' => [
					new Constraints\NotBlank([
						'message' => 'Vyplňte prosím město',
						'groups' => ['differentDeliveryAddress'],
					]),
				],
			])
			->add('deliveryPostcode', 'text', [
				'required' => true,
				'constraints' => [
					new Constraints\NotBlank([
						'message' => 'Vyplňte prosím PSČ',
						'groups' => ['differentDeliveryAddress'],
					]),
				],
			])
			->add('note', 'textarea', ['required' => false])
			->add('save', 'submit');
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'orderPersonalInfo';
	}

	/**
	 * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'attr' => ['novalidate' => 'novalidate'],
			'validation_groups' => function (FormInterface $form) {
				$validationGroups = ['Default'];

				$orderData = $form->getData();
				/* @var $data \SS6\ShopBundle\Model\Order\OrderData */

				if ($orderData->companyCustomer) {
					$validationGroups[] = 'companyCustomer';
				}
				if ($orderData->deliveryAddressFilled) {
					$validationGroups[] = 'differentDeliveryAddress';
				}

				return $validationGroups;
			},
		]);
	}

}
