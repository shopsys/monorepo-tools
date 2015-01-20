<?php

namespace SS6\ShopBundle\Model\Product\Availability;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Product\Availability\AvailabilityData;
use SS6\ShopBundle\Model\Product\Availability\AvailabilityRepository;
use SS6\ShopBundle\Model\Product\Availability\AvailabilityService;

class AvailabilityFacade {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Availability\AvailabilityRepository
	 */
	private $availabilityRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Availability\AvailabilityService
	 */
	private $availabilityService;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 * @param \SS6\ShopBundle\Model\Product\Availability\AvailabilityRepository $availabilityRepository
	 * @param \SS6\ShopBundle\Model\Product\Availability\AvailabilityService $availabilityService
	 */
	public function __construct(
		EntityManager $em,
		AvailabilityRepository $availabilityRepository,
		AvailabilityService $availabilityService
	) {
		$this->em = $em;
		$this->availabilityRepository = $availabilityRepository;
		$this->availabilityService = $availabilityService;
	}

	/**
	 * @param int $availabilityId
	 * @return \SS6\ShopBundle\Model\Product\Availability\
	 */
	public function getById($availabilityId) {
		return $this->availabilityRepository->getById($availabilityId);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Availability\AvailabilityData $availabilityData
	 * @return \SS6\ShopBundle\Model\Product\Availability\Availability
	 */
	public function create(AvailabilityData $availabilityData) {
		$availability = $this->availabilityService->create($availabilityData);
		$this->em->persist($availability);
		$this->em->flush();

		return $availability;
	}

	/**
	 * @param int $availabilityId
	 * @param \SS6\ShopBundle\Model\Product\Availability\AvailabilityData $availabilityData
	 * @return \SS6\ShopBundle\Model\Product\Availability\Availability
	 */
	public function edit($availabilityId, AvailabilityData $availabilityData) {
		$availability = $this->availabilityRepository->getById($availabilityId);
		$this->availabilityService->edit($availability, $availabilityData);
		$this->em->flush();

		return $availability;
	}

	/**
	 * @param int $availabilityId
	 */
	public function deleteById($availabilityId) {
		$availability = $this->availabilityRepository->getById($availabilityId);

		$this->em->remove($availability);
		$this->em->flush();
	}

}
