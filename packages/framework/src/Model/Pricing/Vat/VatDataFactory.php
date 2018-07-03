<?php

namespace Shopsys\FrameworkBundle\Model\Pricing\Vat;

class VatDataFactory implements VatDataFactoryInterface
{
    /**
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Vat\VatData
     */
    public function create(): VatData
    {
        return new VatData();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat $vat
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Vat\VatData
     */
    public function createFromVat(Vat $vat): VatData
    {
        $vatData = new VatData();
        $this->fillFromVat($vatData, $vat);

        return $vatData;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Vat\VatData $vatData
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat $vat
     */
    protected function fillFromVat(VatData $vatData, Vat $vat)
    {
        $vatData->name = $vat->getName();
        $vatData->percent = $vat->getPercent();
    }
}
