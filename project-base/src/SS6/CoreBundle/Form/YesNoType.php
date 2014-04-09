<?php

namespace SS6\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class YesNoType extends AbstractType {

	/**
	 * @param OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
			'choices' => array(
				true => 'Yes',
				false => 'No',
			),
			'expanded' => true,
			'empty_value' => false,
		));
	}

	public function getParent() {
		return 'choice';
	}

	public function getName() {
		return 'yes_no';
	}

}
