<?php

namespace Shopsys\FrameworkBundle\Form;

use Shopsys\FrameworkBundle\Form\Transformers\InverseMultipleChoiceTransformer;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InvertChoiceTypeExtension extends AbstractTypeExtension
{
    protected const INVERT_OPTION = 'invert';

    /**
     * {@inheritDoc}
     */
    public function getExtendedType()
    {
        return ChoiceType::class;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        if ($options[static::INVERT_OPTION] && !$options['multiple']) {
            throw new \Shopsys\FrameworkBundle\Component\Form\Exception\InvertedChoiceNotMultipleException(
                'The "invert" option can be enabled only with "multiple" set to true.'
            );
        }

        if ($options[static::INVERT_OPTION]) {
            $builder->addModelTransformer(new InverseMultipleChoiceTransformer($options['choices']));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setRequired(static::INVERT_OPTION)
            ->setAllowedTypes(static::INVERT_OPTION, 'bool')
            ->setDefaults([
                static::INVERT_OPTION => false,
            ]);
    }
}
