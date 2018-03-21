<?php

namespace Shopsys\FrameworkBundle\Model\Country;

use Doctrine\ORM\EntityManagerInterface;
use Shopsys\FrameworkBundle\Component\Domain\Domain;

class CountryFacade
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $em;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Country\CountryRepository
     */
    private $countryRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @param \Shopsys\FrameworkBundle\Model\Country\CountryRepository $countryRepository
     * @param \Shopsys\FrameworkBundle\Component\Domain\Domain $domain
     */
    public function __construct(
        EntityManagerInterface $em,
        CountryRepository $countryRepository,
        Domain $domain
    ) {
        $this->em = $em;
        $this->countryRepository = $countryRepository;
        $this->domain = $domain;
    }

    /**
     * @param int $countryId
     * @return \Shopsys\FrameworkBundle\Model\Country\Country
     */
    public function getById($countryId)
    {
        return $this->countryRepository->getById($countryId);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Country\CountryData $countryData
     * @param int $domainId
     * @return \Shopsys\FrameworkBundle\Model\Country\Country
     */
    public function create(CountryData $countryData, $domainId)
    {
        $country = new Country($countryData, $domainId);
        $this->em->persist($country);
        $this->em->flush($country);

        return $country;
    }

    /**
     * @param int $countryId
     * @param \Shopsys\FrameworkBundle\Model\Country\CountryData $countryData
     * @return \Shopsys\FrameworkBundle\Model\Country\Country
     */
    public function edit($countryId, CountryData $countryData)
    {
        $country = $this->countryRepository->getById($countryId);
        $country->edit($countryData);
        $this->em->flush($country);

        return $country;
    }

    /**
     * @param int $domainId
     * @return \Shopsys\FrameworkBundle\Model\Country\Country[]
     */
    public function getAllByDomainId($domainId)
    {
        return $this->countryRepository->getAllByDomainId($domainId);
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Country\Country[]
     */
    public function getAllOnCurrentDomain()
    {
        return $this->countryRepository->getAllByDomainId($this->domain->getId());
    }
}
