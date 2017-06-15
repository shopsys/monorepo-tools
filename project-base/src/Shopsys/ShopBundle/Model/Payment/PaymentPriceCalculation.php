<?php

namespace Shopsys\ShopBundle\Model\Payment;

use Shopsys\ShopBundle\Model\Pricing\BasePriceCalculation;
use Shopsys\ShopBundle\Model\Pricing\Currency\Currency;
use Shopsys\ShopBundle\Model\Pricing\Price;
use Shopsys\ShopBundle\Model\Pricing\PricingSetting;

class PaymentPriceCalculation
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
     * @param \Shopsys\ShopBundle\Model\Payment\Payment $payment
     * @param \Shopsys\ShopBundle\Model\Pricing\Currency\Currency $currency
     * @param \Shopsys\ShopBundle\Model\Pricing\Price $productsPrice
     * @param int $domainId
     * @return \Shopsys\ShopBundle\Model\Pricing\Price
     */
    public function calculatePrice(
        Payment $payment,
        Currency $currency,
        Price $productsPrice,
        $domainId
    ) {
        if ($this->isFree($productsPrice, $domainId)) {
            return new Price(0, 0);
        }

        return $this->calculateIndependentPrice($payment, $currency);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Payment\Payment $payment
     * @param \Shopsys\ShopBundle\Model\Pricing\Currency\Currency $currency
     * @return \Shopsys\ShopBundle\Model\Pricing\Price
     */
    public function calculateIndependentPrice(
        Payment $payment,
        Currency $currency
    ) {
        return $this->basePriceCalculation->calculateBasePrice(
            $payment->getPrice($currency)->getPrice(),
            $this->pricingSetting->getInputPriceType(),
            $payment->getVat()
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
     * @param \Shopsys\ShopBundle\Model\Payment\Payment[] $payments
     * @param \Shopsys\ShopBundle\Model\Pricing\Currency\Currency $currency
     * @param \Shopsys\ShopBundle\Model\Pricing\Price $productsPrice
     * @param int $domainId
     * @return \Shopsys\ShopBundle\Model\Pricing\Price[]
     */
    public function getCalculatedPricesIndexedByPaymentId(
        array $payments,
        Currency $currency,
        Price $productsPrice,
        $domainId
    ) {
        $paymentsPricesByPaymentId = [];
        foreach ($payments as $payment) {
            $paymentsPricesByPaymentId[$payment->getId()] = $this->calculatePrice(
                $payment,
                $currency,
                $productsPrice,
                $domainId
            );
        }

        return $paymentsPricesByPaymentId;
    }
}
