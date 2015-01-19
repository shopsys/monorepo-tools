<?php

namespace SS6\ShopBundle\Form\Admin\Order;

use SS6\ShopBundle\Form\Admin\Order\OrderItemFormType;
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
		return 'order';
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
			->add('orderNumber', 'text', ['read_only' => true])
			->add('statusId', 'choice', [
				'choices' => $orderStatusChoices,
				'multiple' => false,
				'expanded' => false,
				'required' => true,
			])
			->add('customerId', 'integer', ['required' => false])
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
			->add('companyName', 'text', ['required' => false])
			->add('companyNumber', 'text', ['required' => false])
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
			->add('deliveryContactPerson', 'text', ['required' => false])
			->add('deliveryCompanyName', 'text', ['required' => false])
			->add('deliveryTelephone', 'text', ['required' => false])
			->add('deliveryStreet', 'text', ['required' => false])
			->add('deliveryCity', 'text', ['required' => false])
			->add('deliveryPostcode', 'text', ['required' => false])
			->add('note', 'textarea', ['required' => false])
			->add('items', 'collection', [
				'type' => new OrderItemFormType(),
				'error_bubbling' => false,
				'allow_add' => true,
				'allow_delete' => true,
			])
			->add('save', 'submit');
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
