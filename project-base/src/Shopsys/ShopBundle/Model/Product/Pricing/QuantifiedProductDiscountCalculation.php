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
    private $priceCalculation;

    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\Rounding
     */
    private $rounding;

    public function __construct(
        PriceCalculation $priceCalculation,
        Rounding $rounding
    ) {
        $this->priceCalculation = $priceCalculation;
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
        $priceVatAmount = $this->priceCalculation->getVatAmountByPriceWithVat($priceWithVat, $vat);
        $priceWithoutVat = $priceWithVat - $priceVatAmount;

        return new Price($priceWithoutVat, $priceWithVat);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Order\Item\QuantifiedItemPrice[] $quantifiedItemsPrices
     * @param float|null $discountPercent
     * @return \Shopsys\ShopBundle\Model\Pricing\Price[]
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
