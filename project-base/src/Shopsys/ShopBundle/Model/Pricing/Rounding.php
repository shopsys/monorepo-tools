<?php

namespace Shopsys\FrameworkBundle\Model\Pricing;

class Rounding
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\PricingSetting
     */
    private $pricingSetting;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\PricingSetting $pricingSetting
     */
    public function __construct(PricingSetting $pricingSetting)
    {
        $this->pricingSetting = $pricingSetting;
    }

    /**
     * @param string $priceWithVat
     * @return string
     */
    public function roundPriceWithVat($priceWithVat)
    {
        $roundingType = $this->pricingSetting->getRoundingType();

        switch ($roundingType) {
            case PricingSetting::ROUNDING_TYPE_HUNDREDTHS:
                $roundedPriceWithVat = round($priceWithVat, 2);
                break;

            case PricingSetting::ROUNDING_TYPE_FIFTIES:
                $roundedPriceWithVat = round($priceWithVat * 2, 0) / 2;
                break;

            case PricingSetting::ROUNDING_TYPE_INTEGER:
                $roundedPriceWithVat = round($priceWithVat, 0);
                break;

            default:
                throw new \Shopsys\FrameworkBundle\Model\Pricing\Exception\InvalidRoundingTypeException(
                    sprintf('Rounding type %s is not valid', $roundingType)
                );
        }

        return $roundedPriceWithVat;
    }

    /**
     * @param string $priceWithoutVat
     * @return string
     */
    public function roundPriceWithoutVat($priceWithoutVat)
    {
        return round($priceWithoutVat, 2);
    }

    /**
     * @param string $vatAmount
     * @return string
     */
    public function roundVatAmount($vatAmount)
    {
        return round($vatAmount, 2);
    }
}
