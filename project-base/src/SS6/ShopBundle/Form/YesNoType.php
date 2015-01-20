<?php

namespace SS6\ShopBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class YesNoType extends AbstractType {

	/**
	 * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'choices' => [
				true => 'Ano',
				false => 'Ne',
			],
			'expanded' => true,
			'empty_value' => false,
		]);
	}

	/**
	 * @return string
	 */
	public function getParent() {
		return 'choice';
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'yes_no';
	}

}
