<?php

namespace Shopsys\FrameworkBundle\Model\Order\Item;

use Shopsys\FrameworkBundle\Model\Pricing\Price;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat;

class QuantifiedItemPrice
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Price
     */
    private $unitPrice;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Price
     */
    private $totalPrice;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat
     */
    private $vat;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Price $unitPrice
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Price $totalPrice
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat $vat
     */
    public function __construct(
        Price $unitPrice,
        Price $totalPrice,
        Vat $vat
    ) {
        $this->unitPrice = $unitPrice;
        $this->totalPrice = $totalPrice;
        $this->vat = $vat;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Price
     */
    public function getUnitPrice()
    {
        return $this->unitPrice;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Price
     */
    public function getTotalPrice()
    {
        return $this->totalPrice;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat
     */
    public function getVat()
    {
        return $this->vat;
    }
}
