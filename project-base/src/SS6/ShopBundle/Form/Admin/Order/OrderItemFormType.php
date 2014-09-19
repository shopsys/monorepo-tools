<?php

namespace SS6\ShopBundle\Form\Admin\Order;

use SS6\ShopBundle\Model\Order\Item\OrderItemData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class OrderItemFormType extends AbstractType {

	/**
	 * @return string
	 */
	public function getName() {
		return 'order_item';
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('name', 'text', array(
				'constraints' => array(
					new Constraints\NotBlank(array('message' => 'Vyplňte prosím název')),
				),
				'error_bubbling' => true,
			))
			->add('price', 'money', array(
				'currency' => false,
				'constraints' => array(
					new Constraints\NotBlank(array('message' => 'Vyplňte prosím cenu')),
				),
				'error_bubbling' => true,
			))
			->add('quantity', 'integer', array(
				'constraints' => array(
					new Constraints\NotBlank(array('message' => 'Vyplňte prosím množství')),
					new Constraints\GreaterThan(array('value' => 0, 'message' => 'Množství musí být větší než 0')),
				),
				'error_bubbling' => true,
			));
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
			'data_class' => OrderItemData::class,
			'attr' => array('novalidate' => 'novalidate'),
		));
	}

}
