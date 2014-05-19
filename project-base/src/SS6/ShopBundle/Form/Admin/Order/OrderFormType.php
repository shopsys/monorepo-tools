<?php

namespace SS6\ShopBundle\Form\Admin\Order;

use SS6\ShopBundle\Form\Admin\Order\OrderFormData;
use SS6\ShopBundle\Form\Admin\Order\OrderItemFormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class OrderFormType extends AbstractType {

	/**
	 * @return string
	 */
	public function getName() {
		return 'order';
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 * @SuppressWarnings(PHPMD)
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('id', 'integer', array('read_only' => true))
			->add('orderNumber', 'text', array('read_only' => true))
			->add('customerId', 'integer', array('required' => false))
			->add('firstName', 'text', array(
				'constraints' => array(
					new Constraints\NotBlank(array('message' => 'Vyplňte prosím jméno')),
				)
			))
			->add('lastName', 'text', array(
				'constraints' => array(
					new Constraints\NotBlank(array('message' => 'Vyplňte prosím příjmení')),
				)
			))
			->add('email', 'email', array(
				'constraints' => array(
					new Constraints\NotBlank(array('message' => 'Vyplňte prosím e-mail')),
					new Constraints\Email(array('message' => 'Vyplňte prosím platný e-mail')),
				)
			))
			->add('telephone', 'text', array(
				'constraints' => array(
					new Constraints\NotBlank(array('message' => 'Vyplňte prosím telefon')),
				)
			))
			->add('companyName', 'text', array('required' => false))
			->add('companyNumber', 'text', array('required' => false))
			->add('companyTaxNumber', 'text', array('required' => false))
			->add('street', 'text', array(
				'constraints' => array(
					new Constraints\NotBlank(array('message' => 'Vyplňte prosím ulici')),
				)
			))
			->add('city', 'text', array(
				'constraints' => array(
					new Constraints\NotBlank(array('message' => 'Vyplňte prosím město')),
				)
			))
			->add('zip', 'text', array(
				'constraints' => array(
					new Constraints\NotBlank(array('message' => 'Vyplňte prosím PSČ')),
				)
			))
			->add('deliveryFirstName', 'text', array('required' => false))
			->add('deliveryLastName', 'text', array('required' => false))
			->add('deliveryCompanyName', 'text', array('required' => false))
			->add('deliveryTelephone', 'text', array('required' => false))
			->add('deliveryStreet', 'text', array('required' => false))
			->add('deliveryCity', 'text', array('required' => false))
			->add('deliveryZip', 'text', array('required' => false))
			->add('note', 'textarea', array('required' => false))
			->add('items', 'collection', array(
				'type' => new OrderItemFormType(),
				))
			->add('submit', 'submit');
	}

	/**
	 * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
			'data_class' => OrderFormData::class,
			'attr' => array('novalidate' => 'novalidate'),
		));
	}

}
