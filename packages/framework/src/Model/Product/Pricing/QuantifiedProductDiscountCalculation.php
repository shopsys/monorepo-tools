<?php

namespace Shopsys\FrameworkBundle\Model\Product\Pricing;

use Shopsys\FrameworkBundle\Component\Money\Money;
use Shopsys\FrameworkBundle\Model\Order\Item\QuantifiedItemPrice;
use Shopsys\FrameworkBundle\Model\Pricing\Price;
use Shopsys\FrameworkBundle\Model\Pricing\PriceCalculation;
use Shopsys\FrameworkBundle\Model\Pricing\Rounding;

class QuantifiedProductDiscountCalculation
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
    public function __construct(
        PriceCalculation $priceCalculation,
        Rounding $rounding
    ) {
        $this->priceCalculation = $priceCalculation;
        $this->rounding = $rounding;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\QuantifiedItemPrice $quantifiedItemPrice
     * @param float $discountPercent
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Price
     */
    protected function calculateDiscount(QuantifiedItemPrice $quantifiedItemPrice, $discountPercent)
    {
        $vat = $quantifiedItemPrice->getVat();
        $priceWithVat = $this->rounding->roundPriceWithVat(
            Money::fromValue($quantifiedItemPrice->getTotalPrice()->getPriceWithVat()->toValue() * $discountPercent / 100)
        )->toValue();
        $priceVatAmount = $this->priceCalculation->getVatAmountByPriceWithVat(Money::fromValue($priceWithVat), $vat)->toValue();
        $priceWithoutVat = $priceWithVat - $priceVatAmount;

        return new Price(Money::fromValue($priceWithoutVat), Money::fromValue($priceWithVat));
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\QuantifiedItemPrice[] $quantifiedItemsPrices
     * @param float|null $discountPercent
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Price[]
     */
    public function calculateDiscounts(array $quantifiedItemsPrices, $discountPercent)
    {
        $quantifiedItemsDiscounts = [];
        foreach ($quantifiedItemsPrices as $quantifiedItemIndex => $quantifiedItemPrice) {
            if ($discountPercent === 0.0 || $discountPercent === null) {
                $quantifiedItemsDiscounts[$quantifiedItemIndex] = null;
            } else {
                $quantifiedItemsDiscounts[$quantifiedItemIndex] = $this->calculateDiscount($quantifiedItemPrice, $discountPercent);
            }
        }

        return $quantifiedItemsDiscounts;
    }
}
