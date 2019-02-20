<?php

namespace Shopsys\FrameworkBundle\Model\Pricing;

use Shopsys\FrameworkBundle\Component\Money\Money;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat;

class BasePriceCalculation
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\PriceCalculation
     */
    protected $priceCalculation;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Rounding
     */
    protected $rounding;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\PriceCalculation $priceCalculation
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Rounding $rounding
     */
    public function __construct(PriceCalculation $priceCalculation, Rounding $rounding)
    {
        $this->priceCalculation = $priceCalculation;
        $this->rounding = $rounding;
    }

    /**
     * @param string $inputPrice
     * @param int $inputPriceType
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat $vat
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Price
     */
    public function calculateBasePrice($inputPrice, $inputPriceType, Vat $vat)
    {
        $basePriceWithVat = $this->getBasePriceWithVat($inputPrice, $inputPriceType, $vat);
        $vatAmount = $this->priceCalculation->getVatAmountByPriceWithVat($basePriceWithVat, $vat);
        $basePriceWithoutVat = $this->rounding->roundPriceWithoutVat($basePriceWithVat - $vatAmount);

        return new Price(Money::fromValue($basePriceWithoutVat), Money::fromValue($basePriceWithVat));
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Price $price
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat $vat
     * @param string[] $coefficients
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Price
     */
    public function applyCoefficients(Price $price, Vat $vat, array $coefficients)
    {
        $priceWithVatBeforeRounding = $price->getPriceWithVat()->toValue();
        foreach ($coefficients as $coefficient) {
            $priceWithVatBeforeRounding *= $coefficient;
        }
        $priceWithVat = $this->rounding->roundPriceWithVat($priceWithVatBeforeRounding);
        $vatAmount = $this->priceCalculation->getVatAmountByPriceWithVat($priceWithVat, $vat);
        $priceWithoutVat = $this->rounding->roundPriceWithoutVat($priceWithVat - $vatAmount);

        return new Price(Money::fromValue($priceWithoutVat), Money::fromValue($priceWithVat));
    }

    /**
     * @param string $inputPrice
     * @param int $inputPriceType
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat $vat
     * @return string
     */
    protected function getBasePriceWithVat($inputPrice, $inputPriceType, Vat $vat)
    {
        switch ($inputPriceType) {
            case PricingSetting::INPUT_PRICE_TYPE_WITH_VAT:
                return $this->rounding->roundPriceWithVat($inputPrice);

            case PricingSetting::INPUT_PRICE_TYPE_WITHOUT_VAT:
                return $this->rounding->roundPriceWithVat($this->priceCalculation->applyVatPercent($inputPrice, $vat));

            default:
                throw new \Shopsys\FrameworkBundle\Model\Pricing\Exception\InvalidInputPriceTypeException();
        }
    }
}
