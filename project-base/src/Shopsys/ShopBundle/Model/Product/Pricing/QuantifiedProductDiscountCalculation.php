<?php

namespace Shopsys\ShopBundle\Model\Product\Pricing;

use Shopsys\ShopBundle\Model\Order\Item\QuantifiedItemPrice;
use Shopsys\ShopBundle\Model\Pricing\Price;
use Shopsys\ShopBundle\Model\Pricing\PriceCalculation;
use Shopsys\ShopBundle\Model\Pricing\Rounding;

class QuantifiedProductDiscountCalculation
{
    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\PriceCalculation
     */
    private $priceCalulation;

    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\Rounding
     */
    private $rounding;

    public function __construct(
        PriceCalculation $priceCalulation,
        Rounding $rounding
    ) {
        $this->priceCalulation = $priceCalulation;
        $this->rounding = $rounding;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Order\Item\QuantifiedItemPrice $quantifiedItemPrice
     * @param float $discountPercent
     * @return \Shopsys\ShopBundle\Model\Pricing\Price
     */
    private function calculateDiscount(QuantifiedItemPrice $quantifiedItemPrice, $discountPercent)
    {
        $vat = $quantifiedItemPrice->getVat();
        $priceWithVat = $this->rounding->roundPriceWithVat(
            $quantifiedItemPrice->getTotalPrice()->getPriceWithVat() * $discountPercent / 100
        );
        $priceVatAmount = $this->priceCalulation->getVatAmountByPriceWithVat($priceWithVat, $vat);
        $priceWithoutVat = $priceWithVat - $priceVatAmount;

        return new Price($priceWithoutVat, $priceWithVat);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Order\Item\QuantifiedItemPrice[quantifiedItemIndex] $quantifiedItemsPrices
     * @param float|null $discountPercent
     * @return \Shopsys\ShopBundle\Model\Pricing\Price[quantifiedItemIndex]
     */
    public function calculateDiscounts(array $quantifiedItemsPrices, $discountPercent)
    {
        $quantifiedItemsDiscounts = [];
        foreach ($quantifiedItemsPrices as $index => $quantifiedItemPrice) {
            if ($discountPercent === 0.0 || $discountPercent === null) {
                $quantifiedItemsDiscounts[$index] = null;
            } else {
                $quantifiedItemsDiscounts[$index] = $this->calculateDiscount($quantifiedItemPrice, $discountPercent);
            }
        }

        return $quantifiedItemsDiscounts;
    }
}
