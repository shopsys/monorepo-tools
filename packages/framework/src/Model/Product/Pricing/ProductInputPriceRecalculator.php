<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Model\Product\Pricing;

use Shopsys\FrameworkBundle\Model\Pricing\BasePriceCalculation;
use Shopsys\FrameworkBundle\Model\Pricing\InputPriceCalculation;

class ProductInputPriceRecalculator
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\BasePriceCalculation
     */
    protected $basePriceCalculation;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\InputPriceCalculation
     */
    protected $inputPriceCalculation;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\BasePriceCalculation $basePriceCalculation
     * @param \Shopsys\FrameworkBundle\Model\Pricing\InputPriceCalculation $inputPriceCalculation
     */
    public function __construct(
        BasePriceCalculation $basePriceCalculation,
        InputPriceCalculation $inputPriceCalculation
    ) {
        $this->basePriceCalculation = $basePriceCalculation;
        $this->inputPriceCalculation = $inputPriceCalculation;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductManualInputPrice $productManualInputPrice
     * @param int $inputPriceType
     * @param string $newVatPercent
     */
    public function recalculateInputPriceForNewVatPercent(
        ProductManualInputPrice $productManualInputPrice,
        int $inputPriceType,
        string $newVatPercent
    ): void {
        if ($productManualInputPrice->getInputPrice() !== null) {
            $basePriceForPricingGroup = $this->basePriceCalculation->calculateBasePrice(
                $productManualInputPrice->getInputPrice(),
                $inputPriceType,
                $productManualInputPrice->getProduct()->getVat()
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
