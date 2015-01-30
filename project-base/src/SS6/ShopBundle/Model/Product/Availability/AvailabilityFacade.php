<?php

namespace SS6\ShopBundle\Model\Product\Availability;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Product\Availability\AvailabilityData;
use SS6\ShopBundle\Model\Product\Availability\AvailabilityRepository;
use SS6\ShopBundle\Model\Product\Availability\AvailabilityService;
use SS6\ShopBundle\Model\Product\Availability\ProductAvailabilityRecalculationScheduler;
use SS6\ShopBundle\Model\Setting\Setting;
use SS6\ShopBundle\Model\Setting\SettingValue;

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
	 * @var \SS6\ShopBundle\Model\Setting\Setting
	 */
	private $setting;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Availability\ProductAvailabilityRecalculationScheduler
	 */
	private $productAvailabilityRecalculationScheduler;

	public function __construct(
		EntityManager $em,
		AvailabilityRepository $availabilityRepository,
		AvailabilityService $availabilityService,
		Setting $setting,
		ProductAvailabilityRecalculationScheduler $productAvailabilityRecalculationScheduler
	) {
		$this->em = $em;
		$this->availabilityRepository = $availabilityRepository;
		$this->availabilityService = $availabilityService;
		$this->setting = $setting;
		$this->productAvailabilityRecalculationScheduler = $productAvailabilityRecalculationScheduler;
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

	/**
	 * @return \SS6\ShopBundle\Model\Product\Availability\Availability
	 */
	public function getDefaultInStockAvailability() {
		$availabilityId = $this->setting->get(Setting::DEFAULT_AVAILABILITY_IN_STOCK, SettingValue::DOMAIN_ID_COMMON);

		return $this->getById($availabilityId);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Availability\Availability $availability
	 */
	public function setDefaultInStockAvailability(Availability $availability) {
		$this->setting->set(Setting::DEFAULT_AVAILABILITY_IN_STOCK, $availability->getId(), SettingValue::DOMAIN_ID_COMMON);
		$this->productAvailabilityRecalculationScheduler->scheduleRecalculateAvailabilityForAllProducts();
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\Availability\Availability[]
	 */
	public function getAll() {
		return $this->availabilityRepository->getAll();
	}

}
