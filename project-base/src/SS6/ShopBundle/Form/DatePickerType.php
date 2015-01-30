<?php

namespace SS6\ShopBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DatePickerType extends AbstractType {

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'widget' => 'single_text',
			'format' => 'dd.MM.yyyy',
		]);
	}

	public function getParent() {
		return 'date';
	}

	public function getName() {
		return 'date_picker';
	}

}
