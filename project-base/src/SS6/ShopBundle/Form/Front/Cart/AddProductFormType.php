<?php

namespace SS6\ShopBundle\Form\Front\Cart;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class AddProductFormType extends AbstractType {

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('productId', 'hidden', array(
					'constraints' => array(
						new Constraints\GreaterThan(0),
						new Constraints\Regex(array('pattern' => '/^\d+$/')),
					),
				)
			)
			->add('quantity', 'text', array(
					'data' => 1,
					'constraints' => array(
						new Constraints\GreaterThan(0),
						new Constraints\Regex(array('pattern' => '/^\d+$/')),
					),
				)
			)
			->add('add', 'submit');
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'addProduct';
	}

	/**
	 * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
			'attr' => array('novalidate' => 'novalidate'),
		));
	}

}
