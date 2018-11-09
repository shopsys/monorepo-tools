<?php

namespace Shopsys\FrameworkBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DisplayOnlyUrlType extends AbstractType
{
    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired(['route', 'route_params', 'route_label', 'domain_id'])
            ->setAllowedTypes('route', ['string'])
            ->setAllowedTypes('route_params', ['array', 'null'])
            ->setAllowedTypes('route_label', ['string', 'null'])
            ->setAllowedTypes('domain_id', ['int', 'null'])
            ->setDefaults([
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'readonly' => 'readonly',
                ],
                'route_params' => [],
                'route_label' => null,
                'domain_id' => null,
            ]);
    }

    /**
     * @inheritdoc
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
        $view->vars['route'] = $options['route'];
        $view->vars['route_params'] = $options['route_params'];
        $view->vars['route_label'] = $options['route_label'];
        $view->vars['domain_id'] = $options['domain_id'];
    }
}
