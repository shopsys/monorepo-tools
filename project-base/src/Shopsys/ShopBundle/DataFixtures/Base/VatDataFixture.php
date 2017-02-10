<?php

namespace Shopsys\ShopBundle\DataFixtures\Base;

use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\ShopBundle\Model\Pricing\Vat\VatData;
use Shopsys\ShopBundle\Model\Pricing\Vat\VatFacade;

class VatDataFixture extends AbstractReferenceFixture
{

    const VAT_ZERO = 'vat_zero';
    const VAT_SECOND_LOW = 'vat_second_low';
    const VAT_LOW = 'vat_low';
    const VAT_HIGH = 'vat_high';

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager) {

        $vatData = new VatData();

        $vatData->name = 'Nulová sazba';
        $vatData->percent = '0';
        $this->createVat($vatData, self::VAT_ZERO);

        $vatData->name = 'Druhá nižší sazba';
        $vatData->percent = '10';
        $this->createVat($vatData, self::VAT_SECOND_LOW);

        $vatData->name = 'Nižší sazba';
        $vatData->percent = '15';
        $this->createVat($vatData, self::VAT_LOW);

        $vatData->name = 'Vyšší sazba';
        $vatData->percent = '21';
        $this->createVat($vatData, self::VAT_HIGH);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Pricing\Vat\VatData $vatData
     * @param string|null $referenceName
     */
    private function createVat(VatData $vatData, $referenceName = null) {
        $vatFacade = $this->get(VatFacade::class);
        /* @var $vatFacade \Shopsys\ShopBundle\Model\Pricing\Vat\VatFacade */

        $vat = $vatFacade->create($vatData);
        if ($referenceName !== null) {
            $this->addReference($referenceName, $vat);
        }
    }
}
