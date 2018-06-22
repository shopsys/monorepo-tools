<?php

namespace Shopsys\FrameworkBundle\Model\Pricing\Vat;

class VatData
{
    /**
     * @var string|null
     */
    public $name;

    /**
     * @var string|null
     */
    public $percent;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat $vat
     */
    public function setFromEntity(Vat $vat)
    {
        $this->name = $vat->getName();
        $this->percent = $vat->getPercent();
    }
}
