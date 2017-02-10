<?php

namespace Shopsys\ShopBundle\Model\Pricing;

use Shopsys\ShopBundle\Component\Setting\Setting;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class InputPriceRecalculationScheduler
{

    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\InputPriceRecalculator
     */
    private $inputPriceRecalculator;

    /**
     * @var \Shopsys\ShopBundle\Component\Setting\Setting
     */
    private $setting;

    /**
     * @var bool
     */
    private $recalculateInputPricesWithoutVat;

    /**
     * @var bool
     */
    private $recalculateInputPricesWithVat;

    public function __construct(InputPriceRecalculator $inputPriceRecalculator, Setting $setting) {
        $this->inputPriceRecalculator = $inputPriceRecalculator;
        $this->setting = $setting;
    }

    public function scheduleSetInputPricesWithoutVat() {
        $this->recalculateInputPricesWithoutVat = true;
    }

    public function scheduleSetInputPricesWithVat() {
        $this->recalculateInputPricesWithVat = true;
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event) {
        if (!$event->isMasterRequest()) {
            return;
        }

        if ($this->recalculateInputPricesWithoutVat) {
            $this->inputPriceRecalculator->recalculateToInputPricesWithoutVat();
            $this->setting->set(
                PricingSetting::INPUT_PRICE_TYPE,
                PricingSetting::INPUT_PRICE_TYPE_WITHOUT_VAT
            );
        } elseif ($this->recalculateInputPricesWithVat) {
            $this->inputPriceRecalculator->recalculateToInputPricesWithVat();
            $this->setting->set(
                PricingSetting::INPUT_PRICE_TYPE,
                PricingSetting::INPUT_PRICE_TYPE_WITH_VAT
            );
        }
    }
}
