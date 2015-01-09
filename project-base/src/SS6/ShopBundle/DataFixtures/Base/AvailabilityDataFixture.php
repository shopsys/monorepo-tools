<?php

namespace SS6\ShopBundle\DataFixtures\Base;

use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\Model\Product\Availability\Availability;
use SS6\ShopBundle\Model\Product\Availability\AvailabilityData;

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
		$availabilityData->name = array('cs' => 'Připravujeme', 'en' => 'Preparing');
		$this->createAvailability($manager, self::PREPARING, $availabilityData);

		$availabilityData->name = array('cs' => 'Skladem', 'en' => 'In stock');
		$this->createAvailability($manager, self::IN_STOCK, $availabilityData);

		$availabilityData->name = array('cs' => 'Na dotaz', 'en' => 'On request');
		$this->createAvailability($manager, self::ON_REQUEST, $availabilityData);

		$availabilityData->name = array('cs' => 'Nedostupné', 'en' => 'Out of stock');
		$this->createAvailability($manager, self::OUT_OF_STOCK, $availabilityData);

		$manager->flush();
	}

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 * @param string $referenceName
	 * @param \SS6\ShopBundle\Model\Product\Availability\AvailabilityData $availabilityData
	 */
	private function createAvailability(ObjectManager $manager, $referenceName, AvailabilityData $availabilityData) {
		$availability = new Availability($availabilityData);
		$manager->persist($availability);
		$this->addReference($referenceName, $availability);
	}

}
