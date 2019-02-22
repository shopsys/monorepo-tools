<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Model\Pricing;

use Shopsys\FrameworkBundle\Component\Money\Money;

class InputPriceCalculation
{
    const INPUT_PRICE_SCALE = 6;

    /**
     * @param int $inputPriceType
     * @param \Shopsys\FrameworkBundle\Component\Money\Money $basePriceWithVat
     * @param string $vatPercent
     * @return \Shopsys\FrameworkBundle\Component\Money\Money
     */
    public function getInputPrice(int $inputPriceType, Money $basePriceWithVat, string $vatPercent): Money
    {
        if ($inputPriceType === PricingSetting::INPUT_PRICE_TYPE_WITHOUT_VAT) {
            $inputPrice = $this->getInputPriceWithoutVat($basePriceWithVat, $vatPercent);
        } elseif ($inputPriceType === PricingSetting::INPUT_PRICE_TYPE_WITH_VAT) {
            $inputPrice = $basePriceWithVat->round(self::INPUT_PRICE_SCALE);
        } else {
            throw new \Shopsys\FrameworkBundle\Model\Pricing\Exception\InvalidInputPriceTypeException(
                sprintf('Input price type "%s" is not valid', $inputPriceType)
            );
        }

        return $inputPrice;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Money\Money $basePriceWithVat
     * @param string $vatPercent
     * @return \Shopsys\FrameworkBundle\Component\Money\Money
     */
    protected function getInputPriceWithoutVat(Money $basePriceWithVat, string $vatPercent): Money
    {
        $divisor = (string)(1 + $vatPercent / 100);

        return $basePriceWithVat->divide($divisor, self::INPUT_PRICE_SCALE);
    }
}
