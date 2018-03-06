<?php

namespace Shopsys\FrameworkBundle\Form;

use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DomainType extends AbstractType
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @param \Shopsys\FrameworkBundle\Component\Domain\Domain $domain
     */
    public function __construct(Domain $domain)
    {
        $this->domain = $domain;
    }

    /**
     * @inheritdoc
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['domainConfigs'] = $this->domain->getAll();
        $view->vars['displayUrl'] = $options['displayUrl'];
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'displayUrl' => false,
        ]);
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return IntegerType::class;
    }
}
