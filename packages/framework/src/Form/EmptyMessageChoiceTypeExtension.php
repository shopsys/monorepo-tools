<?php

namespace Shopsys\FrameworkBundle\Form;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmptyMessageChoiceTypeExtension extends AbstractTypeExtension
{
    /**
     * @param \Symfony\Component\Form\FormView $view
     * @param \Symfony\Component\Form\FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
        $view->vars['empty_message'] = $options['empty_message'];
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('empty_message')
            ->setAllowedTypes('empty_message', 'string')
            ->setDefaults([
                'empty_message' => t('Nothing to choose from.'),
            ]);
    }

    /**
     * @return string
     */
    public function getExtendedType()
    {
        return ChoiceType::class;
    }
}
