<?php

namespace SS6\ShopBundle\Form\Admin\Product;

use SS6\ShopBundle\Form\YesNoType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ProductFormType extends AbstractType {

	/**
	 * @return string
	 */
	public function getName() {
		return 'product';
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('id', 'integer', array('disabled' => true, 'required' => false))
			->add('name', 'text')
			->add('catnum', 'text', array('required' => false))
			->add('partno', 'text', array('required' => false))
			->add('ean', 'text', array('required' => false))
			->add('description', 'textarea', array('required' => false))
			->add('price', 'money', array('currency' => false, 'required' => false))
			->add('sellingFrom', 'datePicker', array('required' => false))
			->add('sellingTo', 'datePicker', array('required' => false))
			->add('stockQuantity', 'integer', array('required' => false))
			->add('hidden', new YesNoType(), array('required' => false))
			->add('save', 'submit');
	}

}
