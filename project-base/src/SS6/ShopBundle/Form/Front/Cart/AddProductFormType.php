<?php

namespace SS6\ShopBundle\Form\Front\Cart;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;

class AddProductFormType extends AbstractType {

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('product_id', 'hidden', array(
					'constraints' => array(
						new Constraints\GreaterThan(0),
						new Constraints\Regex(array('pattern' => '/^\d+$/')),
					)
				)
			)
			->add('quantity', 'integer', array(
					'data' => 1,
					'constraints' => array(
						new Constraints\GreaterThan(0),
					)
				)
			)
			->add('add', 'submit');
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'front_login';
	}

	/**
	 * @param array $options
	 * @return array
	 */
	public function getDefaultOptions(array $options) {
		return array();
	}

}
