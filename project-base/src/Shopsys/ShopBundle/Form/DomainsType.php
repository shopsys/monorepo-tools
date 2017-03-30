<?php

namespace Shopsys\ShopBundle\Form;

use Shopsys\ShopBundle\Component\Domain\Domain;
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
        $choices = [];
        foreach ($this->domain->getAll() as $domainConfig) {
            $choices[$domainConfig->getName()] = $domainConfig->getId();
        }

        $resolver->setDefaults([
            'choices' => $choices,
            'choice_name' => function ($choice) {
                return $choice; // Domain ID
            },
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
