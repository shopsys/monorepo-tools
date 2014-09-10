<?php

namespace SS6\ShopBundle\DataFixtures\Base;

use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Model\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\Model\Product\Availability\Availability;
use SS6\ShopBundle\Model\Product\Availability\AvailabilityData;

class AvailabilityDataFixture extends AbstractReferenceFixture {

	const IN_STOCK = 'availability_in_stock';
	const ON_REQUEST = 'availability_on_request';
	const OUT_OF_STOCK = 'availability_out_of_stock';

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$availabilityData = new AvailabilityData('Skladem');
		$this->createAvailability($manager, self::IN_STOCK, $availabilityData);

		$availabilityData->setName('Na dotaz');
		$this->createAvailability($manager, self::ON_REQUEST, $availabilityData);

		$availabilityData->setName('Nedostupné');
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
