<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Model\Pricing;

use Shopsys\FrameworkBundle\Component\Money\Money;

class Rounding
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\PricingSetting
     */
    protected $pricingSetting;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\PricingSetting $pricingSetting
     */
    public function __construct(PricingSetting $pricingSetting)
    {
        $this->pricingSetting = $pricingSetting;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Money\Money $priceWithVat
     * @return \Shopsys\FrameworkBundle\Component\Money\Money
     */
    public function roundPriceWithVat(Money $priceWithVat): Money
    {
        $roundingType = $this->pricingSetting->getRoundingType();

        switch ($roundingType) {
            case PricingSetting::ROUNDING_TYPE_HUNDREDTHS:
                return $priceWithVat->round(2);

            case PricingSetting::ROUNDING_TYPE_FIFTIES:
                return $priceWithVat->multiply(2)->round(0)->divide(2, 1);

            case PricingSetting::ROUNDING_TYPE_INTEGER:
                return $priceWithVat->round(0);

            default:
                throw new \Shopsys\FrameworkBundle\Model\Pricing\Exception\InvalidRoundingTypeException(
                    sprintf('Rounding type %s is not valid', $roundingType)
                );
        }
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Money\Money $priceWithoutVat
     * @return \Shopsys\FrameworkBundle\Component\Money\Money
     */
    public function roundPriceWithoutVat(Money $priceWithoutVat): Money
    {
        return $priceWithoutVat->round(2);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Money\Money $vatAmount
     * @return \Shopsys\FrameworkBundle\Component\Money\Money
     */
    public function roundVatAmount(Money $vatAmount): Money
    {
        return $vatAmount->round(2);
    }
}
