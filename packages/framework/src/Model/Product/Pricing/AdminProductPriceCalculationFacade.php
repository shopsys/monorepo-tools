<?php

namespace Shopsys\FrameworkBundle\Model\Product\Pricing;

use Shopsys\FrameworkBundle\Model\Pricing\BasePriceCalculation;
use Shopsys\FrameworkBundle\Model\Pricing\PricingSetting;
use Shopsys\FrameworkBundle\Model\Product\Product;

class AdminProductPriceCalculationFacade
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\BasePriceCalculation
     */
    private $basePriceCalculation;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\PricingSetting
     */
    private $pricingSetting;

    public function __construct(BasePriceCalculation $basePriceCalculation, PricingSetting $pricingSetting)
    {
        $this->basePriceCalculation = $basePriceCalculation;
        $this->pricingSetting = $pricingSetting;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Price
     */
    public function calculateProductBasePrice(Product $product)
    {
        if ($product->getPriceCalculationType() !== Product::PRICE_CALCULATION_TYPE_AUTO) {
            throw new \Shopsys\FrameworkBundle\Model\Product\Pricing\Exception\ProductBasePriceCalculationException(
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
