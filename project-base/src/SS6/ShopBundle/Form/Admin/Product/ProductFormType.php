<?php

namespace SS6\ShopBundle\Form\Admin\Product;

use SS6\ShopBundle\Form\YesNoType;
use SS6\ShopBundle\Model\Product\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProductFormType extends AbstractType {

	public function getName() {
		return 'product';
	}

	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('id', 'integer', array('disabled' => true))
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

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
			'data_class' => Product::class,
		));
	}

}
