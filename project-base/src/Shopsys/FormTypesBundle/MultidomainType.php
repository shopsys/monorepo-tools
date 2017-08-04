<?php

namespace Shopsys\FormTypesBundle;

use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Component\Utils;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MultidomainType extends AbstractType
{
    /**
     * @var \Shopsys\ShopBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @param \Shopsys\ShopBundle\Component\Domain\Domain $domain
     */
    public function __construct(Domain $domain)
    {
        $this->domain = $domain;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        Utils::setArrayDefaultValue($options['entry_options'], 'required', $options['required']);
        Utils::setArrayDefaultValue($options['entry_options'], 'constraints', []);

        $subOptions = $options['entry_options'];
        $subOptions['required'] = $options['required'] && $subOptions['required'];

        foreach ($this->domain->getAll() as $domainConfig) {
            if (array_key_exists($domainConfig->getId(), $options['optionsByDomainId'])) {
                $domainOptions = array_merge($subOptions, $options['optionsByDomainId'][$domainConfig->getId()]);
            } else {
                $domainOptions = $subOptions;
            }

            $builder->add($domainConfig->getId(), $options['entry_type'], $domainOptions);
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
            'optionsByDomainId' => [],
        ]);
    }

    /**
     * @param \Symfony\Component\Form\FormView $view
     * @param \Symfony\Component\Form\FormInterface $form
     * @param array $options
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        parent::finishView($view, $form, $options);

        foreach ($view->children as $domainId => $child) {
            $child->vars['domainConfig'] = $this->domain->getDomainConfigById($domainId);
        }
    }
}
