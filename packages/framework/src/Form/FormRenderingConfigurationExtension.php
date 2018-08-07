<?php

namespace Shopsys\FrameworkBundle\Form;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormRenderingConfigurationExtension extends AbstractTypeExtension
{
    const DISPLAY_FORMAT_MULTIDOMAIN_ROWS_NO_PADDING = 'multidomain_form_rows_no_padding';

    /**
     * @param \Symfony\Component\Form\FormView $view
     * @param \Symfony\Component\Form\FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
        $view->vars['macro'] = $options['macro'];
        $view->vars['icon_title'] = $options['icon_title'];
        $view->vars['display_format'] = $options['display_format'];
        $view->vars['js_container'] = $options['js_container'];
        $view->vars['is_plugin_data_group'] = $options['is_plugin_data_group'];
        $view->vars['is_group_container_to_render_as_the_last_one'] = $options['is_group_container_to_render_as_the_last_one'];
        $view->vars['render_form_row'] = $options['render_form_row'];
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'macro' => null,
            'icon_title' => null,
            'display_format' => null,
            'js_container' => null,
            'is_plugin_data_group' => false,
            'is_group_container_to_render_as_the_last_one' => false,
            'render_form_row' => true,
        ]);
    }

    /**
     * @return string
     */
    public function getExtendedType()
    {
        return FormType::class;
    }
}
