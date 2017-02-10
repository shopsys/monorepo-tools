<?php

namespace Shopsys\ShopBundle\Model\Product\Pricing;

use Shopsys\ShopBundle\Model\Pricing\InputPriceCalculation;
use Shopsys\ShopBundle\Model\Product\Pricing\ProductPriceCalculation;
use Shopsys\ShopBundle\Model\Product\Product;

class ProductInputPriceService
{
    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\InputPriceCalculation
     */
    private $inputPriceCalculation;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Pricing\ProductPriceCalculation
     */
    private $productPriceCalculation;

    public function __construct(
        InputPriceCalculation $inputPriceCalculation,
        ProductPriceCalculation $productPriceCalculation
    ) {
        $this->inputPriceCalculation = $inputPriceCalculation;
        $this->productPriceCalculation = $productPriceCalculation;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product $product
     * @param int $inputPriceType
     * @param \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup[] $pricingGroups
     * @param \Shopsys\ShopBundle\Model\Product\Pricing\ProductManualInputPrice[] $manualInputPrices
     * @return string[pricingGroupId]
     */
    public function getManualInputPricesData(
        Product $product,
        $inputPriceType,
        array $pricingGroups,
        array $manualInputPrices
    ) {
        $manualInputPricesData = [];

        if ($product->getPriceCalculationType() === Product::PRICE_CALCULATION_TYPE_AUTO) {
            foreach ($pricingGroups as $pricingGroup) {
                $pricingGroupId = $pricingGroup->getId();
                $productPrice = $this->productPriceCalculation->calculatePrice(
                    $product,
                    $pricingGroup->getDomainId(),
                    $pricingGroup
                );

                $manualInputPricesData[$pricingGroupId] = $this->inputPriceCalculation->getInputPrice(
                    $inputPriceType,
                    $productPrice->getPriceWithVat(),
                    $product->getVat()->getPercent()
                );
            }
        } elseif ($product->getPriceCalculationType() === Product::PRICE_CALCULATION_TYPE_MANUAL) {
            foreach ($manualInputPrices as $manualInputPrice) {
                $pricingGroupId = $manualInputPrice->getPricingGroup()->getId();
                $manualInputPricesData[$pricingGroupId] = $manualInputPrice->getInputPrice();
            }
        }

        return $manualInputPricesData;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product $product
     * @param int $inputPriceType
     * @param \Shopsys\ShopBundle\Model\Product\Pricing\ProductManualInputPrice[] $manualInputPricesInDefaultCurrency
     * @return string|null
     */
    public function getInputPrice(Product $product, $inputPriceType, array $manualInputPricesInDefaultCurrency)
    {
        if ($product->getPriceCalculationType() === Product::PRICE_CALCULATION_TYPE_AUTO) {
            return $product->getPrice();
        } elseif ($product->getPriceCalculationType() === Product::PRICE_CALCULATION_TYPE_MANUAL) {
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
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product $product
     * @param \Shopsys\ShopBundle\Model\Product\Pricing\ProductManualInputPrice[] $manualInputPricesInDefaultCurrency
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
