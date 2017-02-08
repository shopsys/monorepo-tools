<?php

namespace SS6\ShopBundle\Model\Product\Availability;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Component\Setting\Setting;
use SS6\ShopBundle\Model\Product\Availability\AvailabilityData;
use SS6\ShopBundle\Model\Product\Availability\AvailabilityRepository;
use SS6\ShopBundle\Model\Product\Availability\AvailabilityService;
use SS6\ShopBundle\Model\Product\Availability\ProductAvailabilityRecalculationScheduler;
use SS6\ShopBundle\Model\Product\ProductRepository;

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
	 * @var \SS6\ShopBundle\Component\Setting\Setting
	 */
	private $setting;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Availability\ProductAvailabilityRecalculationScheduler
	 */
	private $productAvailabilityRecalculationScheduler;

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductRepository
	 */
	private $productRepository;

	public function __construct(
		EntityManager $em,
		AvailabilityRepository $availabilityRepository,
		AvailabilityService $availabilityService,
		Setting $setting,
		ProductAvailabilityRecalculationScheduler $productAvailabilityRecalculationScheduler,
		ProductRepository $productRepository
	) {
		$this->em = $em;
		$this->availabilityRepository = $availabilityRepository;
		$this->availabilityService = $availabilityService;
		$this->setting = $setting;
		$this->productAvailabilityRecalculationScheduler = $productAvailabilityRecalculationScheduler;
		$this->productRepository = $productRepository;
	}

	/**
	 * @param int $availabilityId
	 * @return \SS6\ShopBundle\Model\Product\Availability\Availability
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
	 * @param int|null $newAvailabilityId
	 */
	public function deleteById($availabilityId, $newAvailabilityId = null) {
		$availability = $this->availabilityRepository->getById($availabilityId);

		if ($newAvailabilityId !== null) {
			$newAvailability = $this->availabilityRepository->getById($newAvailabilityId);

			$this->availabilityRepository->replaceAvailability($availability, $newAvailability);
			if ($this->isAvailabilityDefault($availability)) {
				$this->setDefaultInStockAvailability($newAvailability);
			}
		}

		$this->em->remove($availability);
		$this->em->flush();
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\Availability\Availability
	 */
	public function getDefaultInStockAvailability() {
		$availabilityId = $this->setting->get(Setting::DEFAULT_AVAILABILITY_IN_STOCK);

		return $this->getById($availabilityId);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Availability\Availability $availability
	 */
	public function setDefaultInStockAvailability(Availability $availability) {
		$this->setting->set(Setting::DEFAULT_AVAILABILITY_IN_STOCK, $availability->getId());
		$this->productAvailabilityRecalculationScheduler->scheduleAllProductsForDelayedRecalculation();
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\Availability\Availability[]
	 */
	public function getAll() {
		return $this->availabilityRepository->getAll();
	}

	/**
	 * @param int $availabilityId
	 * @return \SS6\ShopBundle\Model\Product\Availability\Availability[]
	 */
	public function getAllExceptId($availabilityId) {
		return $this->availabilityRepository->getAllExceptId($availabilityId);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Availability\Availability $availability
	 * @return bool
	 */
	public function isAvailabilityUsed(Availability $availability) {
		return $this->availabilityRepository->isAvailabilityUsed($availability);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Availability\Availability $availability
	 * @return bool
	 */
	public function isAvailabilityDefault(Availability $availability) {
		return $this->getDefaultInStockAvailability() === $availability;
	}

}
