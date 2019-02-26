<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Model\Product\Pricing;

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
     * @param string $discountPercent
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Price|null
     */
    protected function calculateDiscount(QuantifiedItemPrice $quantifiedItemPrice, string $discountPercent): ?Price
    {
        $vat = $quantifiedItemPrice->getVat();
        $multiplier = (string)($discountPercent / 100);
        $priceWithVat = $this->rounding->roundPriceWithVat(
            $quantifiedItemPrice->getTotalPrice()->getPriceWithVat()->multiply($multiplier)
        );

        if ($priceWithVat->isZero()) {
            return null;
        }

        $priceVatAmount = $this->priceCalculation->getVatAmountByPriceWithVat($priceWithVat, $vat);
        $priceWithoutVat = $priceWithVat->subtract($priceVatAmount);

        return new Price($priceWithoutVat, $priceWithVat);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\QuantifiedItemPrice[] $quantifiedItemsPrices
     * @param string|null $discountPercent
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Price[]
     */
    public function calculateDiscounts(array $quantifiedItemsPrices, ?string $discountPercent): array
    {
        $quantifiedItemsDiscounts = [];
        foreach ($quantifiedItemsPrices as $quantifiedItemIndex => $quantifiedItemPrice) {
            if ($discountPercent === null) {
                $quantifiedItemsDiscounts[$quantifiedItemIndex] = null;
            } else {
                $quantifiedItemsDiscounts[$quantifiedItemIndex] = $this->calculateDiscount($quantifiedItemPrice, $discountPercent);
            }
        }

        return $quantifiedItemsDiscounts;
    }
}
