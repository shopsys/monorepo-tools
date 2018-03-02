<?php

namespace Shopsys\FrameworkBundle\Model\Pricing;

class DelayedPricingSetting
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\PricingSetting
     */
    private $pricingSetting;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\InputPriceRecalculationScheduler
     */
    private $inputPriceRecalculationScheduler;

    public function __construct(
        PricingSetting $pricingSetting,
        InputPriceRecalculationScheduler $inputPriceRecalculationScheduler
    ) {
        $this->pricingSetting = $pricingSetting;
        $this->inputPriceRecalculationScheduler = $inputPriceRecalculationScheduler;
    }

    /**
     * @param int $inputPriceType
     */
    public function scheduleSetInputPriceType($inputPriceType)
    {
        if (!in_array($inputPriceType, PricingSetting::getInputPriceTypes(), true)) {
            throw new \Shopsys\FrameworkBundle\Model\Pricing\Exception\InvalidInputPriceTypeException('Unknown input price type');
        }

        $currentInputPriceType = $this->pricingSetting->getInputPriceType();

        if ($currentInputPriceType !== $inputPriceType) {
            switch ($inputPriceType) {
                case PricingSetting::INPUT_PRICE_TYPE_WITHOUT_VAT:
                    $this->inputPriceRecalculationScheduler->scheduleSetInputPricesWithoutVat();
                    break;

                case PricingSetting::INPUT_PRICE_TYPE_WITH_VAT:
                    $this->inputPriceRecalculationScheduler->scheduleSetInputPricesWithVat();
                    break;
            }
        }
    }
}
