<?php

namespace Shopsys\ShopBundle\Form;

use Shopsys\ShopBundle\Form\FormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SingleCheckboxChoiceType extends AbstractType {

	public function getParent() {
		return 'choice';
	}

	public function getName() {
		return 'single_checkbox_choice';
	}

	/**
	 * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'multiple' => false,
			'expanded' => true,
		]);
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		parent::buildForm($builder, $options);

		foreach ($builder->all() as $i => $child) {
			/* @var $child \Symfony\Component\Form\FormBuilderInterface */
			$options = $child->getOptions();
			$builder->remove($i);
			$options['required'] = false;
			$builder->add($i, FormType::CHECKBOX, $options);
		}
	}

}
