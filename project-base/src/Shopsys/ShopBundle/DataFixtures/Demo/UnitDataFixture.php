<?php

namespace Shopsys\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\ShopBundle\Model\Product\Unit\UnitData;

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
     * @param \Shopsys\ShopBundle\Model\Product\Unit\UnitData $unitData
     * @param string|null $referenceName
     */
    private function createUnit(UnitData $unitData, $referenceName = null)
    {
        $unitFacade = $this->get('shopsys.shop.product.unit.unit_facade');
        /* @var $unitFacade \Shopsys\ShopBundle\Model\Product\Unit\UnitFacade */

        $unit = $unitFacade->create($unitData);
        if ($referenceName !== null) {
            $this->addReference($referenceName, $unit);
        }
    }
}
