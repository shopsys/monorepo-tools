<?php

namespace SS6\ShopBundle\Form\Admin\Order;

use SS6\ShopBundle\Form\Admin\Order\OrderItemFormType;
use SS6\ShopBundle\Form\FormType;
use SS6\ShopBundle\Model\Order\OrderData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class OrderFormType extends AbstractType {

	/**
	 * @var \SS6\ShopBundle\Model\Order\Status\OrderStatus[]
	 */
	private $allOrderStatuses;

	/**
	 * @param array $allOrderStatuses
	 */
	public function __construct(array $allOrderStatuses) {
		$this->allOrderStatuses = $allOrderStatuses;
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
		$orderStatusChoices = [];
		foreach ($this->allOrderStatuses as $orderStatus) {
			/* @var $orderStatus \SS6\ShopBundle\Model\Order\Status\OrderStatus */
			$orderStatusChoices[$orderStatus->getId()] = $orderStatus->getName();
		}

		$builder
			->add('orderNumber', FormType::TEXT, ['read_only' => true])
			->add('statusId', FormType::CHOICE, [
				'choices' => $orderStatusChoices,
				'multiple' => false,
				'expanded' => false,
				'required' => true,
			])
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
			->add('companyName', FormType::TEXT, ['required' => false])
			->add('companyNumber', FormType::TEXT, ['required' => false])
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
			->add('deliveryContactPerson', FormType::TEXT, ['required' => false])
			->add('deliveryCompanyName', FormType::TEXT, ['required' => false])
			->add('deliveryTelephone', FormType::TEXT, ['required' => false])
			->add('deliveryStreet', FormType::TEXT, ['required' => false])
			->add('deliveryCity', FormType::TEXT, ['required' => false])
			->add('deliveryPostcode', FormType::TEXT, ['required' => false])
			->add('note', FormType::TEXTAREA, ['required' => false])
			->add('items', FormType::COLLECTION, [
				'type' => new OrderItemFormType(),
				'error_bubbling' => false,
				'allow_add' => true,
				'allow_delete' => true,
			])
			->add('save', FormType::SUBMIT);
	}

	/**
	 * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'data_class' => OrderData::class,
			'attr' => ['novalidate' => 'novalidate'],
		]);
	}

}
