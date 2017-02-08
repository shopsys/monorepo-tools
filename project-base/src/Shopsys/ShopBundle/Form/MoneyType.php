<?php

namespace SS6\ShopBundle\Form;

use SS6\ShopBundle\Component\Transformers\RemoveWhitespacesTransformer;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

class MoneyType extends AbstractTypeExtension {

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder->addViewTransformer(new RemoveWhitespacesTransformer());
	}

	/**
	 * @return string
	 */
	public function getExtendedType() {
		return 'money';
	}

}
