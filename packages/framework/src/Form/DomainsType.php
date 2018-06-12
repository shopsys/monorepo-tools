<?php

namespace Shopsys\FrameworkBundle\Form;

use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

class DomainsType extends AbstractType
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

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($this->domain->getAll() as $domainConfig) {
            $builder->add($domainConfig->getId(), CheckboxType::class, [
                'required' => false,
                'label' => $domainConfig->getName(),
            ]);
        }
    }
}
