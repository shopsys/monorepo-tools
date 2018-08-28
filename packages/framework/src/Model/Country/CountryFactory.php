<?php

namespace Shopsys\FrameworkBundle\Model\Country;

use Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver;

class CountryFactory implements CountryFactoryInterface
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
     * @param \Shopsys\FrameworkBundle\Model\Country\CountryData $data
     * @param int $domainId
     * @return \Shopsys\FrameworkBundle\Model\Country\Country
     */
    public function create(CountryData $data, int $domainId): Country
    {
        $classData = $this->entityNameResolver->resolve(Country::class);

        return new $classData($data, $domainId);
    }
}
