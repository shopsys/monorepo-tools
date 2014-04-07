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
			->add('name', 'text', array('label' => 'Název'))
			->add('price', 'money', array('label' => 'Cena', 'currency' => false))
			->add('description', 'textarea', array('label' => 'Popis', 'required' => false))
			->add('save', 'submit', array('label' => 'Uložit'));
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
			'data_class' => 'SS6\CoreBundle\Model\Product\Entity\Product',
		));
	}

}
