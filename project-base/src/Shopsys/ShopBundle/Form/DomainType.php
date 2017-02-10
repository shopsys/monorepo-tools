<?php

namespace Shopsys\ShopBundle\Form;

use Shopsys\ShopBundle\Component\Domain\Domain;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DomainType extends AbstractType
{

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @param \Shopsys\ShopBundle\Component\Domain\Domain $domain
     */
    public function __construct(Domain $domain) {
        $this->domain = $domain;
    }

    /**
     * @inheritdoc
     */
    public function buildView(FormView $view, FormInterface $form, array $options) {
        $view->vars['domainConfigs'] = $this->domain->getAll();
        $view->vars['displayUrl'] = $options['displayUrl'];
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults([
            'displayUrl' => false,
        ]);
    }

    /**
     * @return string
     */
    public function getParent() {
        return 'integer';
    }

    /**
     * @return string
     */
    public function getName() {
        return 'domain';
    }

}
