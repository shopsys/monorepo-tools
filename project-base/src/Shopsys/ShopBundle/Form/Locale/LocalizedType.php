<?php

namespace Shopsys\ShopBundle\Form\Locale;

use Shopsys\ShopBundle\Component\Utils;
use Shopsys\ShopBundle\Model\Localization\Localization;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class LocalizedType extends AbstractType {

    /**
     * @var \Shopsys\ShopBundle\Model\Localization\Localization
     */
    private $localization;

    /**
     * @param \Shopsys\ShopBundle\Model\Localization\Localization $localization
     */
    public function __construct(Localization $localization) {
        $this->localization = $localization;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        Utils::setArrayDefaultValue($options['options'], 'required', $options['required']);
        Utils::setArrayDefaultValue($options['options'], 'constraints', []);

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
