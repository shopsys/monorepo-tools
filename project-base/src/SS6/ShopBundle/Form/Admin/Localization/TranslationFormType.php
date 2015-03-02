<?php

namespace SS6\ShopBundle\Form\Admin\Localization;

use SS6\ShopBundle\Component\Translation\Translator;
use SS6\ShopBundle\Form\FormType;
use SS6\ShopBundle\Model\Localization\Localization;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TranslationFormType extends AbstractType implements DataTransformerInterface {

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
			->add(Translator::SOURCE_LOCALE, FormType::TEXTAREA, ['required' => false]);

		$this->addTypesForOtherLocales($builder);
	}

	/**
	 * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'attr' => ['novalidate' => 'novalidate'],
		]);
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 */
	private function addTypesForOtherLocales(FormBuilderInterface $builder) {
		foreach ($this->localization->getAllLocales() as $locale) {
			if ($locale !== Translator::SOURCE_LOCALE) {
				$builder->add(
					$builder
						->create($locale, FormType::TEXTAREA, ['required' => false])
						->addModelTransformer($this)
				);
			}
		}
	}
}
