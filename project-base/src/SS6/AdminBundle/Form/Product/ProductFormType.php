<?php

namespace SS6\AdminBundle\Form\Product;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProductFormType extends AbstractType {

	public function getName() {
		return 'product';
	}

	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('name', 'text')
			->add('price', 'money', array('currency' => false, 'required' => false))
			->add('description', 'textarea', array('required' => false))
			->add('save', 'submit');
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
			'data_class' => 'SS6\CoreBundle\Model\Product\Entity\Product',
		));
	}

}
