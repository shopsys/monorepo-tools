<?php

namespace SS6\ShopBundle\Form\Admin\Product;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class QuickSearchFormType extends AbstractType {

	/**
	 * @return string
	 */
	public function getName() {
		return 'q';
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->setMethod('GET')
			->add('text', 'text', [
				'required' => false,
			])
			->add('save', 'submit');
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'attr' => ['novalidate' => 'novalidate'],
			'csrf_protection' => false,
		]);
	}

}
