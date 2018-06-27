<?php

namespace Shopsys\FrameworkBundle\Model\Pricing\Vat;

interface VatDataFactoryInterface
{
    /**
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Vat\VatData
     */
    public function create(): VatData;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat $vat
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Vat\VatData
     */
    public function createFromVat(Vat $vat): VatData;
}
