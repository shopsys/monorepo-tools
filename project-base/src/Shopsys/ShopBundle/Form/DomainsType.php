<?php

namespace Shopsys\ShopBundle\Form;

use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Form\Extension\IndexedChoiceList;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DomainsType extends AbstractType
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
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $ids = [];
        $labels = [];
        $values = [];
        foreach ($this->domain->getAll() as $domainConfig) {
            $ids[] = $domainConfig->getId();
            $labels[] = $domainConfig->getName();
            $values[] = (string)$domainConfig->getId();
        }

        $resolver->setDefaults([
            'choice_list' => new IndexedChoiceList($ids, $labels, $ids, $values),
            'multiple' => true,
            'expanded' => true,
        ]);
    }

    /**
     * @return string
     */
    public function getParent() {
        return 'choice';
    }

    /**
     * @return string
     */
    public function getName() {
        return 'domains';
    }

}
