<?php

namespace SS6\ShopBundle\DataFixtures\Base;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Model\Product\Parameter\Parameter;
use SS6\ShopBundle\Model\Product\Parameter\ParameterData;

class ParameterDataFixture extends AbstractFixture {

	const HEIGHT = 'parameter_height';
	const WIDTH = 'parameter_width';
	const DEPTH = 'parameter_depth';

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$availabilityData = new ParameterData('Výška');
		$this->createParameter($manager, self::HEIGHT, $availabilityData);

		$availabilityData->setName('Šířka');
		$this->createParameter($manager, self::WIDTH, $availabilityData);

		$availabilityData->setName('Hloubka');
		$this->createParameter($manager, self::DEPTH, $availabilityData);

		$manager->flush();
	}

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 * @param string $referenceName
	 * @param \SS6\ShopBundle\Model\Product\Parameter\ParameterData $availabilityData
	 */
	private function createParameter(ObjectManager $manager, $referenceName, ParameterData $availabilityData) {
		$availability = new Parameter($availabilityData);
		$manager->persist($availability);
		$this->addReference($referenceName, $availability);
	}

}
