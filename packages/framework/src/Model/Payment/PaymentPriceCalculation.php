<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Model\Payment;

use Shopsys\FrameworkBundle\Model\Pricing\BasePriceCalculation;
use Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency;
use Shopsys\FrameworkBundle\Model\Pricing\Price;
use Shopsys\FrameworkBundle\Model\Pricing\PricingSetting;

class PaymentPriceCalculation
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
     * @param \Shopsys\FrameworkBundle\Model\Payment\Payment $payment
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency $currency
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Price $productsPrice
     * @param int $domainId
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Price
     */
    public function calculatePrice(
        Payment $payment,
        Currency $currency,
        Price $productsPrice,
        int $domainId
    ): Price {
        if ($this->isFree($productsPrice, $domainId)) {
            return Price::zero();
        }

        return $this->calculateIndependentPrice($payment, $currency);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Payment\Payment $payment
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency $currency
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Price
     */
    public function calculateIndependentPrice(Payment $payment, Currency $currency): Price
    {
        return $this->basePriceCalculation->calculateBasePrice(
            $payment->getPrice($currency)->getPrice(),
            $this->pricingSetting->getInputPriceType(),
            $payment->getVat()
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
     * @param \Shopsys\FrameworkBundle\Model\Payment\Payment[] $payments
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency $currency
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Price $productsPrice
     * @param int $domainId
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Price[]
     */
    public function getCalculatedPricesIndexedByPaymentId(
        array $payments,
        Currency $currency,
        Price $productsPrice,
        int $domainId
    ): array {
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
