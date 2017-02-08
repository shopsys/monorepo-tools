<?php

namespace Shopsys\ShopBundle\Model\Country;

use Doctrine\ORM\EntityManager;
use Shopsys\ShopBundle\Component\Domain\Domain;

class CountryFacade {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \Shopsys\ShopBundle\Model\Country\CountryRepository
	 */
	private $countryRepository;

	/**
	 * @var \Shopsys\ShopBundle\Component\Domain\Domain
	 */
	private $domain;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 * @param \Shopsys\ShopBundle\Model\Country\CountryRepository $countryRepository
	 * @param \Shopsys\ShopBundle\Component\Domain\Domain $domain
	 */
	public function __construct(
		EntityManager $em,
		CountryRepository $countryRepository,
		Domain $domain
	) {
		$this->em = $em;
		$this->countryRepository = $countryRepository;
		$this->domain = $domain;
	}

	/**
	 * @param int $countryId
	 * @return \Shopsys\ShopBundle\Model\Country\Country
	 */
	public function getById($countryId) {
		return $this->countryRepository->getById($countryId);
	}

	/**
	 * @param \Shopsys\ShopBundle\Model\Country\CountryData $countryData
	 * @param int $domainId
	 * @return \Shopsys\ShopBundle\Model\Country\Country
	 */
	public function create(CountryData $countryData, $domainId) {
		$country = new Country($countryData, $domainId);
		$this->em->persist($country);
		$this->em->flush($country);

		return $country;
	}

	/**
	 * @param int $countryId
	 * @param \Shopsys\ShopBundle\Model\Country\CountryData $countryData
	 * @return \Shopsys\ShopBundle\Model\Country\Country
	 */
	public function edit($countryId, CountryData $countryData) {
		$country = $this->countryRepository->getById($countryId);
		$country->edit($countryData);
		$this->em->flush($country);

		return $country;
	}

	/**
	 * @param int $domainId
	 * @return \Shopsys\ShopBundle\Model\Country\Country[]
	 */
	public function getAllByDomainId($domainId) {
		return $this->countryRepository->getAllByDomainId($domainId);
	}

	/**
	 * @return \Shopsys\ShopBundle\Model\Country\Country[]
	 */
	public function getAllOnCurrentDomain() {
		return $this->countryRepository->getAllByDomainId($this->domain->getId());
	}

}
