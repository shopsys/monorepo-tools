<?php

namespace Shopsys\FrameworkBundle\Model\Country;

class CountryDataFactory implements CountryDataFactoryInterface
{
    /**
     * @return \Shopsys\FrameworkBundle\Model\Country\CountryData
     */
    public function create(): CountryData
    {
        return new CountryData();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Country\Country $country
     * @return \Shopsys\FrameworkBundle\Model\Country\CountryData
     */
    public function createFromCountry(Country $country): CountryData
    {
        $countryData = new CountryData();
        $this->fillFromCountry($countryData, $country);

        return $countryData;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Country\CountryData $countryData
     * @param \Shopsys\FrameworkBundle\Model\Country\Country $country
     */
    protected function fillFromCountry(CountryData $countryData, Country $country)
    {
        $countryData->name = $country->getName();
        $countryData->code = $country->getCode();
    }
}
