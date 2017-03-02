<?php

namespace Shopsys\ShopBundle\Model\Product\Pricing;

use Shopsys\ShopBundle\Model\Pricing\BasePriceCalculation;
use Shopsys\ShopBundle\Model\Pricing\PricingSetting;
use Shopsys\ShopBundle\Model\Product\Product;

class AdminProductPriceCalculationFacade
{
    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\BasePriceCalculation
     */
    private $basePriceCalculation;

    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\PricingSetting
     */
    private $pricingSetting;

    public function __construct(BasePriceCalculation $basePriceCalculation, PricingSetting $pricingSetting)
    {
        $this->basePriceCalculation = $basePriceCalculation;
        $this->pricingSetting = $pricingSetting;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product $product
     * @return \Shopsys\ShopBundle\Model\Pricing\Price
     */
    public function calculateProductBasePrice(Product $product)
    {
        if ($product->getPriceCalculationType() !== Product::PRICE_CALCULATION_TYPE_AUTO) {
            throw new \Shopsys\ShopBundle\Model\Product\Pricing\Exception\ProductBasePriceCalculationException(
                'Base price can be calculated only for products with auto calculation type.'
            );
        }

        return $this->basePriceCalculation->calculateBasePrice(
            $product->getPrice(),
            $this->pricingSetting->getInputPriceType(),
            $product->getVat()
        );
    }
}
