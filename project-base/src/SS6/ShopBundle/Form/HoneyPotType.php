<?php

namespace SS6\ShopBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class HoneyPotType extends AbstractType {

	/**
	 * @return string
	 */
	public function getParent() {
		return 'text';
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'honey_pot';
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'mapped' => false,
			'required' => false,
			'constraints' => new Constraints\Blank(['message' => 'This field must be empty']),
		]);
	}

}
