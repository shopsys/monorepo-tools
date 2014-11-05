<?php

namespace SS6\ShopBundle\Form\Locale;

use SS6\ShopBundle\Component\Condition;
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
		Condition::setArrayDefaultValue($options['options'], 'required', false);
		Condition::setArrayDefaultValue($options['options'], 'constraints', array());

		$defaultLocaleOptions = $options['options'];
		$otherLocaleOptions = $options['options'];

		$defaultLocaleOptions['constraints'] = array_merge(
			$defaultLocaleOptions['constraints'],
			$options['sub_constraints']
		);

		$otherLocaleOptions['required'] = $options['required'] && $otherLocaleOptions['required'];

		foreach ($this->localize->getAllLocales() as $locale) {
			if ($locale === $this->localize->getDefaultLocale()) {
				$builder->add($locale, $options['type'], $defaultLocaleOptions);
			} else {
				$builder->add($locale, $options['type'], $otherLocaleOptions);
			}
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
