<?php

namespace Shopsys\FrameworkBundle\Model\Pricing\Vat;

class VatFactory implements VatFactoryInterface
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Vat\VatData $data
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat
     */
    public function create(VatData $data): Vat
    {
        return new Vat($data);
    }
}
