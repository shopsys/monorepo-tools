<?php

namespace Shopsys\FrameworkBundle\Model\Customer;

use Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver;

class DeliveryAddressFactory implements DeliveryAddressFactoryInterface
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver
     */
    protected $entityNameResolver;

    /**
     * @param \Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver $entityNameResolver
     */
    public function __construct(EntityNameResolver $entityNameResolver)
    {
        $this->entityNameResolver = $entityNameResolver;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Customer\DeliveryAddressData $data
     * @return \Shopsys\FrameworkBundle\Model\Customer\DeliveryAddress|null
     */
    public function create(DeliveryAddressData $data): ?DeliveryAddress
    {
        if (!$data->addressFilled) {
            return null;
        }

        $classData = $this->entityNameResolver->resolve(DeliveryAddress::class);

        return new $classData($data);
    }
}
