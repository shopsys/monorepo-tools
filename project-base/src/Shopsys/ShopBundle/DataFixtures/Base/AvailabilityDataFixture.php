<?php

namespace SS6\ShopBundle\DataFixtures\Base;

use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\Model\Product\Availability\Availability;
use SS6\ShopBundle\Model\Product\Availability\AvailabilityData;
use SS6\ShopBundle\Model\Product\Availability\AvailabilityFacade;

class AvailabilityDataFixture extends AbstractReferenceFixture {

	const IN_STOCK = 'availability_in_stock';
	const ON_REQUEST = 'availability_on_request';
	const OUT_OF_STOCK = 'availability_out_of_stock';
	const PREPARING = 'availability_preparing';

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$availabilityData = new AvailabilityData();
		$availabilityData->name = ['cs' => 'Připravujeme', 'en' => 'Preparing'];
		$availabilityData->dispatchTime = 14;
		$this->createAvailability($availabilityData, self::PREPARING);

		$availabilityData->name = ['cs' => 'Skladem', 'en' => 'In stock'];
		$availabilityData->dispatchTime = 0;
		$this->createAvailability($availabilityData, self::IN_STOCK);

		$availabilityData->name = ['cs' => 'Na dotaz', 'en' => 'On request'];
		$availabilityData->dispatchTime = 7;
		$this->createAvailability($availabilityData, self::ON_REQUEST);

		$availabilityData->name = ['cs' => 'Nedostupné', 'en' => 'Out of stock'];
		$availabilityData->dispatchTime = null;
		$this->createAvailability($availabilityData, self::OUT_OF_STOCK);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Availability\AvailabilityData $availabilityData
	 * @param string|null $referenceName
	 */
	private function createAvailability(AvailabilityData $availabilityData, $referenceName = null) {
		$availabilityFacade = $this->get(AvailabilityFacade::class);
		/* @var $availabilityFacade \SS6\ShopBundle\Model\Product\Availability\AvailabilityFacade */

		$availability = $availabilityFacade->create($availabilityData);
		if ($referenceName !== null) {
			$this->addReference($referenceName, $availability);
		}
	}

}
