<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Model\Transport;

use Shopsys\FrameworkBundle\Model\Pricing\BasePriceCalculation;
use Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency;
use Shopsys\FrameworkBundle\Model\Pricing\Price;
use Shopsys\FrameworkBundle\Model\Pricing\PricingSetting;

class TransportPriceCalculation
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\BasePriceCalculation
     */
    protected $basePriceCalculation;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\PricingSetting
     */
    protected $pricingSetting;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\BasePriceCalculation $basePriceCalculation
     * @param \Shopsys\FrameworkBundle\Model\Pricing\PricingSetting $pricingSetting
     */
    public function __construct(
        BasePriceCalculation $basePriceCalculation,
        PricingSetting $pricingSetting
    ) {
        $this->pricingSetting = $pricingSetting;
        $this->basePriceCalculation = $basePriceCalculation;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Transport\Transport $transport
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency $currency
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Price $productsPrice
     * @param int $domainId
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Price
     */
    public function calculatePrice(
        Transport $transport,
        Currency $currency,
        Price $productsPrice,
        int $domainId
    ): Price {
        if ($this->isFree($productsPrice, $domainId)) {
            return Price::zero();
        }

        return $this->calculateIndependentPrice($transport, $currency);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Transport\Transport $transport
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency $currency
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Price
     */
    public function calculateIndependentPrice(
        Transport $transport,
        Currency $currency
    ): Price {
        return $this->basePriceCalculation->calculateBasePrice(
            $transport->getPrice($currency)->getPrice(),
            $this->pricingSetting->getInputPriceType(),
            $transport->getVat()
        );
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Price $productsPrice
     * @param int $domainId
     * @return bool
     */
    protected function isFree(Price $productsPrice, int $domainId): bool
    {
        $freeTransportAndPaymentPriceLimit = $this->pricingSetting->getFreeTransportAndPaymentPriceLimit($domainId);

        if ($freeTransportAndPaymentPriceLimit === null) {
            return false;
        }

        return $productsPrice->getPriceWithVat()->isGreaterThanOrEqualTo($freeTransportAndPaymentPriceLimit);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Transport\Transport[] $transports
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency $currency
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Price $productsPrice
     * @param int $domainId
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Price[]
     */
    public function getCalculatedPricesIndexedByTransportId(
        array $transports,
        Currency $currency,
        Price $productsPrice,
        int $domainId
    ): array {
        $transportsPricesByTransportId = [];
        foreach ($transports as $transport) {
            $transportsPricesByTransportId[$transport->getId()] = $this->calculatePrice(
                $transport,
                $currency,
                $productsPrice,
                $domainId
            );
        }

        return $transportsPricesByTransportId;
    }
}
