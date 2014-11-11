<?php

namespace SS6\ShopBundle\Form\Admin\Localization;

use SS6\ShopBundle\Component\Translator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TranslationFormType extends AbstractType implements DataTransformerInterface {

	/**
	 * @param string $value
	 * @return string
	 */
	public function transform($value) {
		if (preg_match('/^' . preg_quote(Translator::NOT_TRANSLATED_PREFIX) . '/u', $value)) {
			return '';
		}

		return $value;
	}

	/**
	 * @param string $value
	 * @return string
	 */
	public function reverseTransform($value) {
		return $value;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'translation';
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('cs', 'textarea', array('required' => false))
			->add($builder
				->create('en', 'textarea', array('required' => false))
				->addModelTransformer($this)
			);
	}

	/**
	 * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
			'attr' => array('novalidate' => 'novalidate'),
		));
	}

}
