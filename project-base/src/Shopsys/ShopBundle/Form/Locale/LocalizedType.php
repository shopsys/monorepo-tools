<?php

namespace SS6\ShopBundle\Form\Locale;

use SS6\ShopBundle\Component\Condition;
use SS6\ShopBundle\Model\Localization\Localization;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class LocalizedType extends AbstractType {

	/**
	 * @var \SS6\ShopBundle\Model\Localization\Localization
	 */
	private $localization;

	/**
	 * @param \SS6\ShopBundle\Model\Localization\Localization $localization
	 */
	public function __construct(Localization $localization) {
		$this->localization = $localization;
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		Condition::setArrayDefaultValue($options['options'], 'required', $options['required']);
		Condition::setArrayDefaultValue($options['options'], 'constraints', []);

		$defaultLocaleOptions = $options['options'];
		$otherLocaleOptions = $options['options'];

		$defaultLocaleOptions['constraints'] = array_merge(
			$defaultLocaleOptions['constraints'],
			$options['main_constraints']
		);

		$defaultLocaleOptions['required'] = $options['required'];
		$otherLocaleOptions['required'] = $options['required'] && $otherLocaleOptions['required'];

		foreach ($this->localization->getAllLocales() as $locale) {
			if ($locale === $this->localization->getDefaultLocale()) {
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
		$resolver->setDefaults([
			'compound' => true,
			'options' => [],
			'main_constraints' => [],
			'type' => 'text',
		]);
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'localized';
	}

}
