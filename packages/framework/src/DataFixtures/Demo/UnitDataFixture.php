<?php

namespace Shopsys\FrameworkBundle\DataFixtures\Demo;

use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\FrameworkBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\FrameworkBundle\Model\Product\Unit\UnitData;
use Shopsys\FrameworkBundle\Model\Product\Unit\UnitFacade;

class UnitDataFixture extends AbstractReferenceFixture
{
    const UNIT_CUBIC_METERS = 'unit_m3';

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $unitData = new UnitData();

        $unitData->name = ['cs' => 'm³', 'en' => 'm³'];
        $this->createUnit($unitData, self::UNIT_CUBIC_METERS);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Unit\UnitData $unitData
     * @param string|null $referenceName
     */
    private function createUnit(UnitData $unitData, $referenceName = null)
    {
        $unitFacade = $this->get(UnitFacade::class);
        /* @var $unitFacade \Shopsys\FrameworkBundle\Model\Product\Unit\UnitFacade */

        $unit = $unitFacade->create($unitData);
        if ($referenceName !== null) {
            $this->addReference($referenceName, $unit);
        }
    }
}
