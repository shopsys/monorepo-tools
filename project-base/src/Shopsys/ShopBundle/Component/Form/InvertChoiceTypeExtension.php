<?php

namespace Shopsys\ShopBundle\Component\Form;

use Shopsys\ShopBundle\Component\Transformers\InverseMultipleChoiceTransformer;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InvertChoiceTypeExtension extends AbstractTypeExtension
{
    const INVERT_OPTION = 'invert';

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

        if ($options[self::INVERT_OPTION] && !$options['multiple']) {
            throw new \Shopsys\ShopBundle\Component\Form\Exception\InvertedChoiceNotMultipleException(
                'The "invert" option can be enabled only with "multiple" set to true.'
            );
        }

        if ($options[self::INVERT_OPTION]) {
            $builder->addModelTransformer(new InverseMultipleChoiceTransformer($options['choices']));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setRequired(self::INVERT_OPTION)
            ->setAllowedTypes(self::INVERT_OPTION, 'bool')
            ->setDefaults([
                self::INVERT_OPTION => false,
            ]);
    }
}
