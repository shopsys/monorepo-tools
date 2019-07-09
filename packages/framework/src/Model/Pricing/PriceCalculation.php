<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Model\Pricing;

use Shopsys\FrameworkBundle\Component\Money\Money;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat;

class PriceCalculation
{
    protected const PRICE_CALCULATION_MAX_SCALE = 6;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Rounding
     */
    protected $rounding;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Rounding $rounding
     */
    public function __construct(Rounding $rounding)
    {
        $this->rounding = $rounding;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Money\Money $priceWithVat
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat $vat
     * @return \Shopsys\FrameworkBundle\Component\Money\Money
     */
    public function getVatAmountByPriceWithVat(Money $priceWithVat, Vat $vat): Money
    {
        $divisor = (string)(1 + $vat->getPercent() / 100);

        $priceWithoutVat = $priceWithVat->divide($divisor, static::PRICE_CALCULATION_MAX_SCALE);

        return $this->rounding->roundVatAmount($priceWithVat->subtract($priceWithoutVat));
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Money\Money $priceWithoutVat
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat $vat
     * @return \Shopsys\FrameworkBundle\Component\Money\Money
     */
    public function applyVatPercent(Money $priceWithoutVat, Vat $vat): Money
    {
        $multiplier = (string)(1 + $vat->getPercent() / 100);

        return $priceWithoutVat->multiply($multiplier);
    }
}
