<?php

namespace Shopsys\FrameworkBundle\Model\Country;

interface CountryFactoryInterface
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Country\CountryData $data
     * @return \Shopsys\FrameworkBundle\Model\Country\Country
     */
    public function create(CountryData $data): Country;
}
