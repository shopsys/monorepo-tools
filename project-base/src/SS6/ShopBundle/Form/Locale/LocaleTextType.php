<?php

namespace SS6\ShopBundle\Form\Locale;

use SS6\ShopBundle\Model\Localize\Localize;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class LocaleTextType extends AbstractType {

	/**
	 * @var \SS6\ShopBundle\Model\Localize\Localize
	 */
	private $localize;

	/**
	 * @param \SS6\ShopBundle\Model\Localize\Localize $localize
	 */
	public function __construct(Localize $localize) {
		$this->localize = $localize;
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$subOptions = $options['options'];

		if (!array_key_exists('constraints', $subOptions)) {
			$subOptions['constraints'] = array();
		}
		$subOptions['constraints'] = array_merge(
			$subOptions['constraints'],
			$options['sub_constraints']
		);
		foreach ($this->localize->getAllLocales() as $locale) {
			$builder->add($locale, $options['type'], $subOptions);
		}
	}

	/**
	 * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
			'compound' => true,
			'options' => array(),
			'sub_constraints' => array(),
			'type' => 'text',
		));
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'locale_text';
	}

}
