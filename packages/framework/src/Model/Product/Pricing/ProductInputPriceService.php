<?php

namespace Shopsys\FrameworkBundle\Model\Product\Pricing;

class ProductInputPriceService
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductManualInputPrice[] $manualInputPrices
     * @return string[]
     */
    public function getManualInputPricesDataIndexedByPricingGroupId(array $manualInputPrices)
    {
        $manualInputPricesDataByPricingGroupId = [];

        foreach ($manualInputPrices as $manualInputPrice) {
            $pricingGroupId = $manualInputPrice->getPricingGroup()->getId();
            $manualInputPricesDataByPricingGroupId[$pricingGroupId] = $manualInputPrice->getInputPrice();
        }

        return $manualInputPricesDataByPricingGroupId;
    }
}
