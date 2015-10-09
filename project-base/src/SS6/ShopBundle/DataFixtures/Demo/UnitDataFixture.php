<?php

namespace SS6\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\Model\Product\Unit\Unit;
use SS6\ShopBundle\Model\Product\Unit\UnitData;

class UnitDataFixture extends AbstractReferenceFixture {

	const M3 = 'unit_m3';

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$unitData = new UnitData();

		$unitData->name = ['cs' => 'm³', 'en' => 'm³'];
		$this->createUnit($manager, self::M3, $unitData);

		$manager->flush();
	}

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 * @param string $referenceName
	 * @param \SS6\ShopBundle\Model\Product\Unit\UnitData $unitData
	 */
	private function createUnit(ObjectManager $manager, $referenceName, UnitData $unitData) {
		$unit = new Unit($unitData);
		$manager->persist($unit);
		$this->addReference($referenceName, $unit);
	}

}
