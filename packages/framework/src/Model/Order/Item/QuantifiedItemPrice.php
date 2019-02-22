<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Model\Order\Item;

use Shopsys\FrameworkBundle\Model\Pricing\Price;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat;

class QuantifiedItemPrice
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Price
     */
    protected $unitPrice;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Price
     */
    protected $totalPrice;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat
     */
    protected $vat;

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
    public function getUnitPrice(): Price
    {
        return $this->unitPrice;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Price
     */
    public function getTotalPrice(): Price
    {
        return $this->totalPrice;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat
     */
    public function getVat(): Vat
    {
        return $this->vat;
    }
}
