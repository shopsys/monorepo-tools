<?php

namespace Shopsys\ShopBundle\Form\Admin\Localization;

use Shopsys\ShopBundle\Component\Translation\Translator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class TranslationFormType extends AbstractType implements DataTransformerInterface
{
    /**
     * @var string[]
     */
    private $locales;

    /**
     * @param string[] $locales
     */
    public function __construct(array $locales)
    {
        $this->locales = $locales;
    }

    /**
     * @param string $value
     * @return string
     */
    public function transform($value)
    {
        if (preg_match('/^' . preg_quote(Translator::NOT_TRANSLATED_PREFIX) . '/u', $value)) {
            return '';
        }

        return $value;
    }

    /**
     * @param string $value
     * @return string
     */
    public function reverseTransform($value)
    {
        return $value;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($this->locales as $locale) {
            $builder->add(
                $builder
                    ->create($locale, TextareaType::class, [
                        'required' => true,
                        'constraints' => new Constraints\NotBlank(['message' => 'Please enter translation']),
                    ])
                    ->addModelTransformer($this)
            );
        }
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'attr' => ['novalidate' => 'novalidate'],
        ]);
    }
}
