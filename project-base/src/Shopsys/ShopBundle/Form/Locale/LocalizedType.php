<?php

namespace Shopsys\ShopBundle\Form\Locale;

use Shopsys\ShopBundle\Component\Utils;
use Shopsys\ShopBundle\Model\Localization\Localization;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocalizedType extends AbstractType
{
    /**
     * @var \Shopsys\ShopBundle\Model\Localization\Localization
     */
    private $localization;

    /**
     * @param \Shopsys\ShopBundle\Model\Localization\Localization $localization
     */
    public function __construct(Localization $localization)
    {
        $this->localization = $localization;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        Utils::setArrayDefaultValue($options['entry_options'], 'required', $options['required']);
        Utils::setArrayDefaultValue($options['entry_options'], 'constraints', []);

        $defaultLocaleOptions = $options['entry_options'];
        $otherLocaleOptions = $options['entry_options'];

        $defaultLocaleOptions['constraints'] = array_merge(
            $defaultLocaleOptions['constraints'],
            $options['main_constraints']
        );

        $defaultLocaleOptions['required'] = $options['required'];
        $otherLocaleOptions['required'] = $options['required'] && $otherLocaleOptions['required'];

        foreach ($this->localization->getLocalesOfAllDomains() as $locale) {
            if ($locale === $this->localization->getAdminLocale()) {
                $builder->add($locale, $options['entry_type'], $defaultLocaleOptions);
            } else {
                $builder->add($locale, $options['entry_type'], $otherLocaleOptions);
            }
        }
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'compound' => true,
            'entry_type' => TextType::class,
            'entry_options' => [],
            'main_constraints' => [],
        ]);
    }
}
