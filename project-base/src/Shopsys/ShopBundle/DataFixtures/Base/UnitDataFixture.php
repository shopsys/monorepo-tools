<?php

namespace Shopsys\ShopBundle\DataFixtures\Base;

use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\ShopBundle\Model\Product\Unit\UnitData;
use Shopsys\ShopBundle\Model\Product\Unit\UnitFacade;

class UnitDataFixture extends AbstractReferenceFixture
{

    const PCS = 'unit_pcs';

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager) {
        $unitData = new UnitData();

        $unitData->name = ['cs' => 'ks', 'en' => 'pcs'];
        $this->createUnit($unitData, self::PCS);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Unit\UnitData $unitData
     * @param string|null $referenceName
     */
    private function createUnit(UnitData $unitData, $referenceName = null) {
        $unitFacade = $this->get(UnitFacade::class);
        /* @var $unitFacade \Shopsys\ShopBundle\Model\Product\Unit\UnitFacade */

        $unit = $unitFacade->create($unitData);
        if ($referenceName !== null) {
            $this->addReference($referenceName, $unit);
        }
    }
}
