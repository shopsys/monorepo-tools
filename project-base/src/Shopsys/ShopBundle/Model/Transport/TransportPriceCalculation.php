<?php

namespace Shopsys\ShopBundle\Model\Transport;

use Shopsys\ShopBundle\Model\Pricing\BasePriceCalculation;
use Shopsys\ShopBundle\Model\Pricing\Currency\Currency;
use Shopsys\ShopBundle\Model\Pricing\Price;
use Shopsys\ShopBundle\Model\Pricing\PricingSetting;

class TransportPriceCalculation
{
    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\BasePriceCalculation
     */
    private $basePriceCalculation;

    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\PricingSetting
     */
    private $pricingSetting;

    /**
     * @param \Shopsys\ShopBundle\Model\Pricing\BasePriceCalculation $basePriceCalculation
     * @param \Shopsys\ShopBundle\Model\Pricing\PricingSetting $pricingSetting
     */
    public function __construct(
        BasePriceCalculation $basePriceCalculation,
        PricingSetting $pricingSetting
    ) {
        $this->pricingSetting = $pricingSetting;
        $this->basePriceCalculation = $basePriceCalculation;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Transport\Transport $transport
     * @param \Shopsys\ShopBundle\Model\Pricing\Currency\Currency $currency
     * @param \Shopsys\ShopBundle\Model\Pricing\Price $productsPrice
     * @param int $domainId
     * @return \Shopsys\ShopBundle\Model\Pricing\Price
     */
    public function calculatePrice(
        Transport $transport,
        Currency $currency,
        Price $productsPrice,
        $domainId
    ) {
        if ($this->isFree($productsPrice, $domainId)) {
            return new Price(0, 0);
        }

        return $this->calculateIndependentPrice($transport, $currency);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Transport\Transport $transport
     * @param \Shopsys\ShopBundle\Model\Pricing\Currency\Currency $currency
     * @return \Shopsys\ShopBundle\Model\Pricing\Price
     */
    public function calculateIndependentPrice(
        Transport $transport,
        Currency $currency
    ) {
        return $this->basePriceCalculation->calculateBasePrice(
            $transport->getPrice($currency)->getPrice(),
            $this->pricingSetting->getInputPriceType(),
            $transport->getVat()
        );
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Pricing\Price $productsPrice
     * @param int $domainId
     * @return bool
     */
    private function isFree(Price $productsPrice, $domainId)
    {
        $freeTransportAndPaymentPriceLimit = $this->pricingSetting->getFreeTransportAndPaymentPriceLimit($domainId);

        if ($freeTransportAndPaymentPriceLimit === null) {
            return false;
        }

        return $productsPrice->getPriceWithVat() >= $freeTransportAndPaymentPriceLimit;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Transport\Transport[] $transports
     * @param \Shopsys\ShopBundle\Model\Pricing\Currency\Currency $currency
     * @param \Shopsys\ShopBundle\Model\Pricing\Price $productsPrice
     * @param int $domainId
     * @return \Shopsys\ShopBundle\Model\Pricing\Price[]
     */
    public function getCalculatedPricesIndexedByTransportId(
        array $transports,
        Currency $currency,
        Price $productsPrice,
        $domainId
    ) {
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
