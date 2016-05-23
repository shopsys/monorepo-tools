<?php

namespace SS6\ShopBundle\Form\Front\Customer;

use SS6\ShopBundle\Form\FormType;
use SS6\ShopBundle\Form\ValidationGroup;
use SS6\ShopBundle\Model\Customer\DeliveryAddressData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class DeliveryAddressFormType extends AbstractType {

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
	 * @return string
	 */
	public function getName() {
		return 'delivery_address_form';
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('addressFilled', FormType::CHECKBOX, ['required' => false])
			->add('companyName', FormType::TEXT, [
				'required' => false,
				'constraints' => [
					new Constraints\Length([
						'max' => 100,
						'maxMessage' => 'Název firmy nesmí být delší než {{ limit }} znaků',
						'groups' => [self::VALIDATION_GROUP_DIFFERENT_DELIVERY_ADDRESS],
					]),
				],
			])
			->add('contactPerson', FormType::TEXT, [
				'required' => false,
				'constraints' => [
					new Constraints\Length([
						'max' => 200,
						'maxMessage' => 'Jméno kontaktní osoby nesmí být delší než {{ limit }} znaků',
						'groups' => [self::VALIDATION_GROUP_DIFFERENT_DELIVERY_ADDRESS],
					]),
				],
			])
			->add('telephone', FormType::TEXT, [
				'required' => false,
				'constraints' => [
					new Constraints\Length([
						'max' => 30,
						'maxMessage' => 'Telefon nesmí být delší než {{ limit }} znaků',
						'groups' => [self::VALIDATION_GROUP_DIFFERENT_DELIVERY_ADDRESS],
					]),
				],
			])
			->add('street', FormType::TEXT, [
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
			->add('city', FormType::TEXT, [
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
			->add('postcode', FormType::TEXT, [
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
			->add('country', FormType::CHOICE, [
				'required' => true,
				'choice_list' => new ObjectChoiceList($this->countries, 'name', [], null, 'id'),
			]);
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'data_class' => DeliveryAddressData::class,
			'attr' => ['novalidate' => 'novalidate'],
			'validation_groups' => function (FormInterface $form) {
				$validationGroups = [ValidationGroup::VALIDATION_GROUP_DEFAULT];

				$deliveryAddressData = $form->getData();
				/* @var $customerData \SS6\ShopBundle\Model\Customer\DeliveryAddressData */

				if ($deliveryAddressData->addressFilled) {
					$validationGroups[] = self::VALIDATION_GROUP_DIFFERENT_DELIVERY_ADDRESS;
				}

				return $validationGroups;
			},
		]);
	}

}
