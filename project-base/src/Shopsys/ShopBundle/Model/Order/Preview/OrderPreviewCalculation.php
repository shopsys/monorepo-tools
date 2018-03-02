<?php

namespace Shopsys\FrameworkBundle\Model\Order\Preview;

use Shopsys\FrameworkBundle\Model\Customer\User;
use Shopsys\FrameworkBundle\Model\Order\OrderPriceCalculation;
use Shopsys\FrameworkBundle\Model\Payment\Payment;
use Shopsys\FrameworkBundle\Model\Payment\PaymentPriceCalculation;
use Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency;
use Shopsys\FrameworkBundle\Model\Pricing\Price;
use Shopsys\FrameworkBundle\Model\Product\Pricing\QuantifiedProductDiscountCalculation;
use Shopsys\FrameworkBundle\Model\Product\Pricing\QuantifiedProductPriceCalculation;
use Shopsys\FrameworkBundle\Model\Transport\Transport;
use Shopsys\FrameworkBundle\Model\Transport\TransportPriceCalculation;

class OrderPreviewCalculation
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Pricing\QuantifiedProductPriceCalculation
     */
    private $quantifiedProductPriceCalculation;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Pricing\QuantifiedProductDiscountCalculation
     */
    private $quantifiedProductDiscountCalculation;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Transport\TransportPriceCalculation
     */
    private $transportPriceCalculation;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Payment\PaymentPriceCalculation
     */
    private $paymentPriceCalculation;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\OrderPriceCalculation
     */
    private $orderPriceCalculation;

    public function __construct(
        QuantifiedProductPriceCalculation $quantifiedProductPriceCalculation,
        QuantifiedProductDiscountCalculation $quantifiedProductDiscountCalculation,
        TransportPriceCalculation $transportPriceCalculation,
        PaymentPriceCalculation $paymentPriceCalculation,
        OrderPriceCalculation $orderPriceCalculation
    ) {
        $this->quantifiedProductPriceCalculation = $quantifiedProductPriceCalculation;
        $this->quantifiedProductDiscountCalculation = $quantifiedProductDiscountCalculation;
        $this->transportPriceCalculation = $transportPriceCalculation;
        $this->paymentPriceCalculation = $paymentPriceCalculation;
        $this->orderPriceCalculation = $orderPriceCalculation;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency $currency
     * @param int $domainId
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\QuantifiedProduct[] $quantifiedProducts
     * @param \Shopsys\FrameworkBundle\Model\Transport\Transport|null $transport
     * @param \Shopsys\FrameworkBundle\Model\Payment\Payment|null $payment
     * @param \Shopsys\FrameworkBundle\Model\Customer\User|null $user
     * @param float|null $promoCodeDiscountPercent
     * @return \Shopsys\FrameworkBundle\Model\Order\Preview\OrderPreview
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function calculatePreview(
        Currency $currency,
        $domainId,
        array $quantifiedProducts,
        Transport $transport = null,
        Payment $payment = null,
        User $user = null,
        $promoCodeDiscountPercent = null
    ) {
        $quantifiedItemsPrices = $this->quantifiedProductPriceCalculation->calculatePrices(
            $quantifiedProducts,
            $domainId,
            $user
        );
        $quantifiedItemsDiscounts = $this->quantifiedProductDiscountCalculation->calculateDiscounts(
            $quantifiedItemsPrices,
            $promoCodeDiscountPercent
        );

        $productsPrice = $this->getProductsPrice($quantifiedItemsPrices, $quantifiedItemsDiscounts);

        if ($transport !== null) {
            $transportPrice = $this->transportPriceCalculation->calculatePrice(
                $transport,
                $currency,
                $productsPrice,
                $domainId
            );
        } else {
            $transportPrice = null;
        }

        if ($payment !== null) {
            $paymentPrice = $this->paymentPriceCalculation->calculatePrice(
                $payment,
                $currency,
                $productsPrice,
                $domainId
            );
            $roundingPrice = $this->calculateRoundingPrice(
                $payment,
                $currency,
                $productsPrice,
                $transportPrice,
                $paymentPrice
            );
        } else {
            $paymentPrice = null;
            $roundingPrice = null;
        }

        $totalPrice = $this->calculateTotalPrice(
            $productsPrice,
            $transportPrice,
            $paymentPrice,
            $roundingPrice
        );

        return new OrderPreview(
            $quantifiedProducts,
            $quantifiedItemsPrices,
            $quantifiedItemsDiscounts,
            $productsPrice,
            $totalPrice,
            $transport,
            $transportPrice,
            $payment,
            $paymentPrice,
            $roundingPrice,
            $promoCodeDiscountPercent
        );
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Payment\Payment $payment
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency $currency
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Price $productsPrice
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Price|null $transportPrice
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Price|null $paymentPrice
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Price|null
     */
    private function calculateRoundingPrice(
        Payment $payment,
        Currency $currency,
        Price $productsPrice,
        Price $transportPrice = null,
        Price $paymentPrice = null
    ) {
        $totalPrice = $this->calculateTotalPrice(
            $productsPrice,
            $transportPrice,
            $paymentPrice,
            null
        );

        return $this->orderPriceCalculation->calculateOrderRoundingPrice($payment, $currency, $totalPrice);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Price $productsPrice
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Price|null $transportPrice
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Price|null $paymentPrice
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Price|null $roundingPrice
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Price
     */
    private function calculateTotalPrice(
        Price $productsPrice,
        Price $transportPrice = null,
        Price $paymentPrice = null,
        Price $roundingPrice = null
    ) {
        $totalPrice = new Price(0, 0);

        $totalPrice = $totalPrice->add($productsPrice);

        if ($transportPrice !== null) {
            $totalPrice = $totalPrice->add($transportPrice);
        }

        if ($paymentPrice !== null) {
            $totalPrice = $totalPrice->add($paymentPrice);
        }

        if ($roundingPrice !== null) {
            $totalPrice = $totalPrice->add($roundingPrice);
        }

        return $totalPrice;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\QuantifiedItemPrice[] $quantifiedItemsPrices
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Price[] $quantifiedItemsDiscounts
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Price
     */
    private function getProductsPrice(array $quantifiedItemsPrices, array $quantifiedItemsDiscounts)
    {
        $finalPrice = new Price(0, 0);

        foreach ($quantifiedItemsPrices as $quantifiedItemPrice) {
            $finalPrice = $finalPrice->add($quantifiedItemPrice->getTotalPrice());
        }

        foreach ($quantifiedItemsDiscounts as $discount) {
            if ($discount !== null) {
                /* @var $discount \Shopsys\FrameworkBundle\Model\Pricing\Price */
                $finalPrice = $finalPrice->subtract($discount);
            }
        }

        return $finalPrice;
    }
}
