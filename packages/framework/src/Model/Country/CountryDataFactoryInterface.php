<?php

namespace Shopsys\FrameworkBundle\Model\Country;

interface CountryDataFactoryInterface
{
    /**
     * @return \Shopsys\FrameworkBundle\Model\Country\CountryData
     */
    public function create(): CountryData;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Country\Country $country
     * @return \Shopsys\FrameworkBundle\Model\Country\CountryData
     */
    public function createFromCountry(Country $country): CountryData;
}
