<?php

namespace Shopsys\FrameworkBundle\Model\Product;

use Shopsys\FrameworkBundle\Model\Pricing\BasePriceCalculation;
use Shopsys\FrameworkBundle\Model\Pricing\InputPriceCalculation;
use Shopsys\FrameworkBundle\Model\Pricing\PricingSetting;

class ProductService
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\InputPriceCalculation
     */
    private $inputPriceCalculation;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\BasePriceCalculation
     */
    private $basePriceCalculation;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\PricingSetting
     */
    private $pricingSetting;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductCategoryDomainFactoryInterface
     */
    protected $productCategoryDomainFactory;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\InputPriceCalculation $inputPriceCalculation
     * @param \Shopsys\FrameworkBundle\Model\Pricing\BasePriceCalculation $basePriceCalculation
     * @param \Shopsys\FrameworkBundle\Model\Pricing\PricingSetting $pricingSetting
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductCategoryDomainFactoryInterface $productCategoryDomainFactory
     */
    public function __construct(
        InputPriceCalculation $inputPriceCalculation,
        BasePriceCalculation $basePriceCalculation,
        PricingSetting $pricingSetting,
        ProductCategoryDomainFactoryInterface $productCategoryDomainFactory
    ) {
        $this->inputPriceCalculation = $inputPriceCalculation;
        $this->basePriceCalculation = $basePriceCalculation;
        $this->pricingSetting = $pricingSetting;
        $this->productCategoryDomainFactory = $productCategoryDomainFactory;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductManualInputPrice[] $productManualInputPrices
     * @param string $newVatPercent
     */
    public function recalculateInputPriceForNewVatPercent(Product $product, $productManualInputPrices, $newVatPercent)
    {
        $inputPriceType = $this->pricingSetting->getInputPriceType();

        foreach ($productManualInputPrices as $productManualInputPrice) {
            $basePriceForPricingGroup = $this->basePriceCalculation->calculateBasePrice(
                $productManualInputPrice->getInputPrice(),
                $inputPriceType,
                $product->getVat()
            );
            $inputPriceForPricingGroup = $this->inputPriceCalculation->getInputPrice(
                $inputPriceType,
                $basePriceForPricingGroup->getPriceWithVat(),
                $newVatPercent
            );
            $productManualInputPrice->setInputPrice($inputPriceForPricingGroup);
        }
    }
}
