<?php

namespace SS6\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\Model\Product\Unit\UnitData;
use SS6\ShopBundle\Model\Product\Unit\UnitFacade;

class UnitDataFixture extends AbstractReferenceFixture {

	const M3 = 'unit_m3';

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$unitData = new UnitData();

		$unitData->name = ['cs' => 'm³', 'en' => 'm³'];
		$this->createUnit($unitData, self::M3);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Unit\UnitData $unitData
	 * @param string|null $referenceName
	 */
	private function createUnit(UnitData $unitData, $referenceName = null) {
		$unitFacade = $this->get(UnitFacade::class);
		/* @var $unitFacade \SS6\ShopBundle\Model\Product\Unit\UnitFacade */

		$unit = $unitFacade->create($unitData);
		if ($referenceName !== null) {
			$this->addReference($referenceName, $unit);
		}
	}

}
