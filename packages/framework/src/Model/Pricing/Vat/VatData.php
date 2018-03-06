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
     * @param string|null $name
     * @param string|null $percent
     */
    public function __construct($name = null, $percent = null)
    {
        $this->name = $name;
        $this->percent = $percent;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat $vat
     */
    public function setFromEntity(Vat $vat)
    {
        $this->name = $vat->getName();
        $this->percent = $vat->getPercent();
    }
}
