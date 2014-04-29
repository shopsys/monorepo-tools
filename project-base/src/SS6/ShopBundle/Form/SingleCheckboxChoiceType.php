<?php

namespace SS6\ShopBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SingleCheckboxChoiceType extends AbstractType {

	public function getParent() {
		return 'choice';
	}

	public function getName() {
		return 'single_checkbox_choice';
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
			'multiple' => false,
			'expanded' => true,
		));
	}

	public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options) {
		parent::buildForm($builder, $options);

		foreach ($builder->all() as $i => $child) {
			/* @var $child \Symfony\Component\Form\FormBuilderInterface */
			$options = $child->getOptions();
			$builder->remove($i);
			$options['required'] = false;
			$builder->add($i, 'checkbox', $options);
		}
	}

}
