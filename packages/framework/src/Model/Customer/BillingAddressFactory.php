<?php

namespace Shopsys\FrameworkBundle\Model\Customer;

use Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver;

class BillingAddressFactory implements BillingAddressFactoryInterface
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver
     */
    private $entityNameResolver;

    /**
     * @param \Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver $entityNameResolver
     */
    public function __construct(EntityNameResolver $entityNameResolver)
    {
        $this->entityNameResolver = $entityNameResolver;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Customer\BillingAddressData $data
     * @return \Shopsys\FrameworkBundle\Model\Customer\BillingAddress
     */
    public function create(BillingAddressData $data): BillingAddress
    {
        $classData = $this->entityNameResolver->resolve(BillingAddress::class);

        return new $classData($data);
    }
}
