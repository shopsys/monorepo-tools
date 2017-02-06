<?php

namespace SS6\ShopBundle\Form\Front\Cart;

use SS6\ShopBundle\Component\Constraints\ConstraintValue;
use SS6\ShopBundle\Form\FormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class CartFormType extends AbstractType {

	/**
	 * @var \SS6\ShopBundle\Model\Cart\Cart
	 */
	private $cart;

	public function __construct(\SS6\ShopBundle\Model\Cart\Cart $cart) {
		$this->cart = $cart;
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('quantities', FormType::COLLECTION, [
				'allow_add' => true,
				'allow_delete' => true,
				'type' => FormType::TEXT,
				'constraints' => [
					new Constraints\All([
						'constraints' => [
							new Constraints\NotBlank(['message' => 'Please enter quantity']),
							new Constraints\GreaterThan(['value' => 0, 'message' => 'Quantity must be greater than {{ compared_value }}']),
							new Constraints\LessThanOrEqual([
								'value' => ConstraintValue::INTEGER_MAX_VALUE,
								'message' => 'Please enter valid quantity',
							]),
						],
					]),
				],
			])
			->add('submit', FormType::SUBMIT);
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'cart_form';
	}

	/**
	 * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'attr' => ['novalidate' => 'novalidate'],
		]);
	}

}
