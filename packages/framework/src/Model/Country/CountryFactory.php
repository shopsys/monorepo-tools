<?php

namespace Shopsys\FrameworkBundle\Model\Country;

class CountryFactory implements CountryFactoryInterface
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Country\CountryData $data
     * @param int $domainId
     * @return \Shopsys\FrameworkBundle\Model\Country\Country
     */
    public function create(CountryData $data, int $domainId): Country
    {
        return new Country($data, $domainId);
    }
}
