<?php

declare(strict_types=1);

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
     * @param \Shopsys\FrameworkBundle\Component\Money\Money $inputPrice
     * @param int $inputPriceType
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat $vat
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Price
     */
    public function calculateBasePrice(Money $inputPrice, int $inputPriceType, Vat $vat): Price
    {
        $basePriceWithVat = $this->getBasePriceWithVat($inputPrice, $inputPriceType, $vat);
        $vatAmount = Money::fromValue($this->priceCalculation->getVatAmountByPriceWithVat($basePriceWithVat->toValue(), $vat));
        $basePriceWithoutVat = $this->rounding->roundPriceWithoutVat($basePriceWithVat->subtract($vatAmount));

        return new Price($basePriceWithoutVat, $basePriceWithVat);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Price $price
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat $vat
     * @param string[] $coefficients
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Price
     */
    public function applyCoefficients(Price $price, Vat $vat, array $coefficients): Price
    {
        $priceWithVatBeforeRounding = $price->getPriceWithVat();
        foreach ($coefficients as $coefficient) {
            $priceWithVatBeforeRounding = $priceWithVatBeforeRounding->multiply($coefficient);
        }
        $priceWithVat = $this->rounding->roundPriceWithVat($priceWithVatBeforeRounding);
        $vatAmount = Money::fromValue($this->priceCalculation->getVatAmountByPriceWithVat($priceWithVat->toValue(), $vat));
        $priceWithoutVat = $this->rounding->roundPriceWithoutVat($priceWithVat->subtract($vatAmount));

        return new Price($priceWithoutVat, $priceWithVat);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Money\Money $inputPrice
     * @param int $inputPriceType
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat $vat
     * @return \Shopsys\FrameworkBundle\Component\Money\Money
     */
    protected function getBasePriceWithVat(Money $inputPrice, int $inputPriceType, Vat $vat): Money
    {
        switch ($inputPriceType) {
            case PricingSetting::INPUT_PRICE_TYPE_WITH_VAT:
                return $this->rounding->roundPriceWithVat($inputPrice);

            case PricingSetting::INPUT_PRICE_TYPE_WITHOUT_VAT:
                return $this->rounding->roundPriceWithVat(Money::fromValue($this->priceCalculation->applyVatPercent($inputPrice->toValue(), $vat)));

            default:
                throw new \Shopsys\FrameworkBundle\Model\Pricing\Exception\InvalidInputPriceTypeException();
        }
    }
}
