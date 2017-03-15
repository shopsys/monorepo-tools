<?php

namespace Shopsys\ShopBundle\Form;

use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Form\Extension\IndexedChoiceList;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DomainsType extends AbstractType
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
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
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
    public function getParent()
    {
        return ChoiceType::class;
    }
}
