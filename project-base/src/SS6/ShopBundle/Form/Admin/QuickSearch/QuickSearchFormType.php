<?php

namespace SS6\ShopBundle\Form\Admin\QuickSearch;

use SS6\ShopBundle\Form\FormType;
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
			->add('text', FormType::TEXT, [
				'required' => false,
			])
			->add('save', FormType::SUBMIT);
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'attr' => ['novalidate' => 'novalidate'],
			'csrf_protection' => false,
			'data_class' => QuickSearchFormData::class,
		]);
	}

}
