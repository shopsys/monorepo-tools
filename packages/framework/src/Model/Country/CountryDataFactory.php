<?php

namespace Shopsys\FrameworkBundle\Model\Country;

use Shopsys\FrameworkBundle\Component\Domain\Domain;

class CountryDataFactory implements CountryDataFactoryInterface
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    protected $domain;

    /**
     * @param \Shopsys\FrameworkBundle\Component\Domain\Domain $domain
     */
    public function __construct(Domain $domain)
    {
        $this->domain = $domain;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Country\CountryData
     */
    public function create(): CountryData
    {
        $countryData = new CountryData();
        $this->fillNew($countryData);

        return $countryData;
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
    protected function fillFromCountry(CountryData $countryData, Country $country): void
    {

        /** @var \Shopsys\FrameworkBundle\Model\Country\CountryTranslation[] $translations */
        $translations = $country->getTranslations();

        foreach ($translations as $translation) {
            $countryData->names[$translation->getLocale()] = $translation->getName();
        }

        foreach ($this->domain->getAllIds() as $domainId) {
            $countryData->enabled[$domainId] = $country->isEnabled($domainId);
            $countryData->priority[$domainId] = $country->getPriority($domainId);
        }

        $countryData->code = $country->getCode();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Country\CountryData $countryData
     */
    protected function fillNew(CountryData $countryData): void
    {
        foreach ($this->domain->getAllIds() as $domainId) {
            $countryData->enabled[$domainId] = true;
            $countryData->priority[$domainId] = null;
        }
    }
}
