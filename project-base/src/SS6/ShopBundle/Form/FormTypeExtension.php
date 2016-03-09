<?php

namespace SS6\ShopBundle\Form;

use SS6\ShopBundle\Form\FormType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormTypeExtension extends AbstractTypeExtension {

	/**
	 * @inheritdoc
	 */
	public function configureOptions(OptionsResolver $resolver) {
		$resolver->setDefaults([
			'cascade_validation' => true,
		]);
	}

	/**
	 * @inheritdoc
	 */
	public function getExtendedType() {
		return FormType::FORM;
	}

}
