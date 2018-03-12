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
        $view->vars['is_group_container'] = $options['is_group_container'];
        $view->vars['macro'] = $options['macro'];
        $view->vars['icon_title'] = $options['icon_title'];
        $view->vars['display_format'] = $options['display_format'];
        $view->vars['js_container'] = $options['js_container'];
        $view->vars['is_plugin_data_group'] = $options['is_plugin_data_group'];
        $view->vars['image_preview'] = $options['image_preview'];
        $view->vars['is_group_container_to_render_as_the_last_one'] = $options['is_group_container_to_render_as_the_last_one'];
    }
    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('is_group_container')
            ->addAllowedTypes('is_group_container', 'boolean')
            ->setDefaults(['is_group_container' => false])
            ->setDefaults(['macro' => null])
            ->setDefaults(['icon_title' => null])
            ->setDefaults(['display_format' => null])
            ->setDefaults(['js_container' => null])
            ->setDefaults(['is_plugin_data_group' => false])
            ->setDefaults(['image_preview' => null])
            ->setDefaults(['is_group_container_to_render_as_the_last_one' => false]);
    }
    /**
     * @return string
     */
    public function getExtendedType()
    {
        return FormType::class;
    }
}
