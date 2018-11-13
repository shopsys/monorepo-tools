<?php

namespace Shopsys\FrameworkBundle\Model\Product\Pricing;

use Shopsys\FrameworkBundle\Model\Pricing\InputPriceCalculation;
use Shopsys\FrameworkBundle\Model\Product\Product;

class ProductInputPriceService
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\InputPriceCalculation
     */
    private $inputPriceCalculation;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceCalculation
     */
    private $productPriceCalculation;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\InputPriceCalculation $inputPriceCalculation
     * @param \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceCalculation $productPriceCalculation
     */
    public function __construct(
        InputPriceCalculation $inputPriceCalculation,
        ProductPriceCalculation $productPriceCalculation
    ) {
        $this->inputPriceCalculation = $inputPriceCalculation;
        $this->productPriceCalculation = $productPriceCalculation;
    }

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

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param int $inputPriceType
     * @param \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductManualInputPrice[] $manualInputPricesInDefaultCurrency
     * @return string|null
     */
    public function getInputPrice(Product $product, $inputPriceType, array $manualInputPricesInDefaultCurrency)
    {
        $maxSellingPriceWithVatInDefaultCurrency = $this->getMaxSellingPriceWithVatInDefaultCurrency(
            $product,
            $manualInputPricesInDefaultCurrency
        );

        if ($maxSellingPriceWithVatInDefaultCurrency === null) {
            return null;
        }

        return $this->inputPriceCalculation->getInputPrice(
            $inputPriceType,
            $maxSellingPriceWithVatInDefaultCurrency,
            $product->getVat()->getPercent()
        );
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductManualInputPrice[] $manualInputPricesInDefaultCurrency
     * @return string|null
     */
    private function getMaxSellingPriceWithVatInDefaultCurrency(Product $product, array $manualInputPricesInDefaultCurrency)
    {
        $maxSellingPriceWithVatInDefaultCurrency = null;
        foreach ($manualInputPricesInDefaultCurrency as $manualInputPrice) {
            $pricingGroup = $manualInputPrice->getPricingGroup();
            $productPrice = $this->productPriceCalculation->calculatePrice(
                $product,
                $pricingGroup->getDomainId(),
                $pricingGroup
            );

            if ($maxSellingPriceWithVatInDefaultCurrency === null
                || $productPrice->getPriceWithVat() > $maxSellingPriceWithVatInDefaultCurrency
            ) {
                $maxSellingPriceWithVatInDefaultCurrency = $productPrice->getPriceWithVat();
            }
        }

        return $maxSellingPriceWithVatInDefaultCurrency;
    }
}
