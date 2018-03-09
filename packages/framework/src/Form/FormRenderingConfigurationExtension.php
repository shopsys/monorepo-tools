<?php

namespace Shopsys\FrameworkBundle\Form;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormRenderingConfigurationExtension extends AbstractTypeExtension
{

    /**
     * @param \Symfony\Component\Form\FormView $view
     * @param \Symfony\Component\Form\FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
        $view->vars['is_group_container'] = $options['is_group_container'];
    }
    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('is_group_container')
            ->addAllowedTypes('is_group_container', 'boolean')
            ->setDefaults(['is_group_container' => false]);
    }
    /**
     * @return string
     */
    public function getExtendedType()
    {
        return FormType::class;
    }
}