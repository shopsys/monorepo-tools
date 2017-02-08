<?php

namespace SS6\ShopBundle\Model\Country;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Component\Domain\Domain;

class CountryFacade {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\Country\CountryRepository
	 */
	private $countryRepository;

	/**
	 * @var \SS6\ShopBundle\Component\Domain\Domain
	 */
	private $domain;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 * @param \SS6\ShopBundle\Model\Country\CountryRepository $countryRepository
	 * @param \SS6\ShopBundle\Component\Domain\Domain $domain
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
	 * @return \SS6\ShopBundle\Model\Country\Country
	 */
	public function getById($countryId) {
		return $this->countryRepository->getById($countryId);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Country\CountryData $countryData
	 * @param int $domainId
	 * @return \SS6\ShopBundle\Model\Country\Country
	 */
	public function create(CountryData $countryData, $domainId) {
		$country = new Country($countryData, $domainId);
		$this->em->persist($country);
		$this->em->flush($country);

		return $country;
	}

	/**
	 * @param int $countryId
	 * @param \SS6\ShopBundle\Model\Country\CountryData $countryData
	 * @return \SS6\ShopBundle\Model\Country\Country
	 */
	public function edit($countryId, CountryData $countryData) {
		$country = $this->countryRepository->getById($countryId);
		$country->edit($countryData);
		$this->em->flush($country);

		return $country;
	}

	/**
	 * @param int $domainId
	 * @return \SS6\ShopBundle\Model\Country\Country[]
	 */
	public function getAllByDomainId($domainId) {
		return $this->countryRepository->getAllByDomainId($domainId);
	}

	/**
	 * @return \SS6\ShopBundle\Model\Country\Country[]
	 */
	public function getAllOnCurrentDomain() {
		return $this->countryRepository->getAllByDomainId($this->domain->getId());
	}

}
