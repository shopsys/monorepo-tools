<?php

namespace SS6\ShopBundle\Form\Admin\Order;

use SS6\ShopBundle\Component\Constraints\Email;
use SS6\ShopBundle\Form\Admin\Order\OrderItemFormType;
use SS6\ShopBundle\Form\Admin\Order\OrderTransportFormType;
use SS6\ShopBundle\Form\FormType;
use SS6\ShopBundle\Form\ValidationGroup;
use SS6\ShopBundle\Model\Country\Country;
use SS6\ShopBundle\Model\Order\OrderData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class OrderFormType extends AbstractType {

	const VALIDATION_GROUP_DELIVERY_ADDRESS_SAME_AS_BILLING_ADDRESS = 'deliveryAddressSameAsBillingAddress';

	/**
	 * @var \SS6\ShopBundle\Model\Order\Status\OrderStatus[]
	 */
	private $allOrderStatuses;

	/**
	 * @var \SS6\ShopBundle\Model\Transport\Transport[]
	 */
	private $transports;

	/**
	 * @var \SS6\ShopBundle\Model\Payment\Payment[]
	 */
	private $payments;

	/**
	 * @var \SS6\ShopBundle\Model\Country\Country[]
	 */
	private $countries;

	/**
	 * @param array $allOrderStatuses
	 * @param array $transports
	 * @param array $payments
	 * @param \SS6\ShopBundle\Model\Country\Country[] $countries
	 */
	public function __construct(array $allOrderStatuses, array $transports, array $payments, array $countries) {
		$this->allOrderStatuses = $allOrderStatuses;
		$this->transports = $transports;
		$this->payments = $payments;
		$this->countries = $countries;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'order_form';
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('orderNumber', FormType::TEXT, ['read_only' => true])
			->add('status', FormType::CHOICE, [
				'choice_list' => new ObjectChoiceList($this->allOrderStatuses, 'name', [], null, 'id'),
				'multiple' => false,
				'expanded' => false,
				'required' => true,
			])
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
					new Email(['message' => 'Vyplňte prosím platný e-mail']),
					new Constraints\Length(['max' => 255, 'maxMessage' => 'E-mail nesmí být delší než {{ limit }} znaků']),
				],
			])
			->add('telephone', FormType::TEXT, [
				'constraints' => [
					new Constraints\NotBlank(['message' => 'Vyplňte prosím telefon']),
					new Constraints\Length(['max' => 30, 'maxMessage' => 'Telefon nesmí být delší než {{ limit }} znaků']),
				],
			])
			->add('companyName', FormType::TEXT, [
				'required' => false,
				'constraints' => [
					new Constraints\Length(['max' => 100, 'maxMessage' => 'Název společnosti nesmí být delší než {{ limit }} znaků']),
				],
			])
			->add('companyNumber', FormType::TEXT, [
				'required' => false,
				'constraints' => [
					new Constraints\Length(['max' => 50, 'maxMessage' => 'IČ nesmí být delší než {{ limit }} znaků']),
				],
			])
			->add('companyTaxNumber', FormType::TEXT, [
				'required' => false,
				'constraints' => [
					new Constraints\Length(['max' => 50, 'maxMessage' => 'DIČ nesmí být delší než {{ limit }} znaků']),
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
			->add('deliveryAddressSameAsBillingAddress', FormType::CHECKBOX, ['required' => false])
			->add('deliveryFirstName', FormType::TEXT, [
				'required' => true,
				'constraints' => [
					new Constraints\NotBlank([
						'message' => 'Vyplňte prosím jméno kontaktní osoby',
						'groups' => [self::VALIDATION_GROUP_DELIVERY_ADDRESS_SAME_AS_BILLING_ADDRESS],
					]),
					new Constraints\Length([
						'max' => 100,
						'maxMessage' => 'Jméno kontaktní osoby nesmí být delší než {{ limit }} znaků',
						'groups' => [self::VALIDATION_GROUP_DELIVERY_ADDRESS_SAME_AS_BILLING_ADDRESS],
					]),
				],
			])
			->add('deliveryLastName', FormType::TEXT, [
				'required' => true,
				'constraints' => [
					new Constraints\NotBlank([
						'message' => 'Vyplňte prosím příjemní kontaktní osoby',
						'groups' => [self::VALIDATION_GROUP_DELIVERY_ADDRESS_SAME_AS_BILLING_ADDRESS],
					]),
					new Constraints\Length([
						'max' => 100,
						'maxMessage' => 'Příjmení kontaktní osoby nesmí být delší než {{ limit }} znaků',
						'groups' => [self::VALIDATION_GROUP_DELIVERY_ADDRESS_SAME_AS_BILLING_ADDRESS],
					]),
				],
			])
			->add('deliveryCompanyName', FormType::TEXT, [
				'required' => false,
				'constraints' => [
					new Constraints\Length([
						'max' => 100,
						'maxMessage' => 'Název nesmí být delší než {{ limit }} znaků',
						'groups' => [self::VALIDATION_GROUP_DELIVERY_ADDRESS_SAME_AS_BILLING_ADDRESS],
					]),
				],
			])
			->add('deliveryTelephone', FormType::TEXT, [
				'required' => false,
				'constraints' => [
					new Constraints\Length([
						'max' => 30,
						'maxMessage' => 'Telefon nesmí být delší než {{ limit }} znaků',
						'groups' => [self::VALIDATION_GROUP_DELIVERY_ADDRESS_SAME_AS_BILLING_ADDRESS],
					]),
				],
			])
			->add('deliveryStreet', FormType::TEXT, [
				'required' => true,
				'constraints' => [
					new Constraints\NotBlank([
						'message' => 'Vyplňte prosím ulici',
						'groups' => [self::VALIDATION_GROUP_DELIVERY_ADDRESS_SAME_AS_BILLING_ADDRESS],
					]),
					new Constraints\Length([
						'max' => 100,
						'maxMessage' => 'Název ulice nesmí být delší než {{ limit }} znaků',
						'groups' => [self::VALIDATION_GROUP_DELIVERY_ADDRESS_SAME_AS_BILLING_ADDRESS],
					]),
				],
			])
			->add('deliveryCity', FormType::TEXT, [
				'required' => true,
				'constraints' => [
					new Constraints\NotBlank([
						'message' => 'Vyplňte prosím město',
						'groups' => [self::VALIDATION_GROUP_DELIVERY_ADDRESS_SAME_AS_BILLING_ADDRESS],
					]),
					new Constraints\Length(['max' => 100,
						'maxMessage' => 'Název města nesmí být delší než {{ limit }} znaků',
						'groups' => [self::VALIDATION_GROUP_DELIVERY_ADDRESS_SAME_AS_BILLING_ADDRESS],
					]),
				],
			])
			->add('deliveryPostcode', FormType::TEXT, [
				'required' => true,
				'constraints' => [
					new Constraints\NotBlank([
						'message' => 'Vyplňte prosím PSČ',
						'groups' => [self::VALIDATION_GROUP_DELIVERY_ADDRESS_SAME_AS_BILLING_ADDRESS],
					]),
					new Constraints\Length([
						'max' => 30,
						'maxMessage' => 'PSČ nesmí být delší než {{ limit }} znaků',
						'groups' => [self::VALIDATION_GROUP_DELIVERY_ADDRESS_SAME_AS_BILLING_ADDRESS],
					]),
				],
			])
			->add('deliveryCountry', FormType::CHOICE, [
				'required' => true,
				'choice_list' => new ObjectChoiceList($this->countries, 'name', [], null, 'id'),
				'constraints' => [
					new Constraints\NotBlank(['message' => 'Prosím vyberte stát']),
				],
			])
			->add('note', FormType::TEXTAREA, ['required' => false])
			->add('itemsWithoutTransportAndPayment', FormType::COLLECTION, [
				'type' => new OrderItemFormType(),
				'error_bubbling' => false,
				'allow_add' => true,
				'allow_delete' => true,
			])
			->add('orderPayment', new OrderPaymentFormType($this->payments))
			->add('orderTransport', new OrderTransportFormType($this->transports))
			->add('save', FormType::SUBMIT);
	}

	/**
	 * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'data_class' => OrderData::class,
			'attr' => ['novalidate' => 'novalidate'],
			'validation_groups' => function (FormInterface $form) {
				$validationGroups = [ValidationGroup::VALIDATION_GROUP_DEFAULT];

				$orderData = $form->getData();
				/* @var $data \SS6\ShopBundle\Model\Order\OrderData */

				if (!$orderData->deliveryAddressSameAsBillingAddress) {
					$validationGroups[] = self::VALIDATION_GROUP_DELIVERY_ADDRESS_SAME_AS_BILLING_ADDRESS;
				}

				return $validationGroups;
			},
		]);
	}

}
