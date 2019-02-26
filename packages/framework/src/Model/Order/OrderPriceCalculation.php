<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Model\Order;

use Shopsys\FrameworkBundle\Component\Money\Money;
use Shopsys\FrameworkBundle\Model\Order\Item\OrderItemPriceCalculation;
use Shopsys\FrameworkBundle\Model\Payment\Payment;
use Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency;
use Shopsys\FrameworkBundle\Model\Pricing\Price;
use Shopsys\FrameworkBundle\Model\Pricing\Rounding;

class OrderPriceCalculation
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\Item\OrderItemPriceCalculation
     */
    protected $orderItemPriceCalculation;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Rounding
     */
    protected $rounding;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\OrderItemPriceCalculation $orderItemPriceCalculation
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Rounding $rounding
     */
    public function __construct(
        OrderItemPriceCalculation $orderItemPriceCalculation,
        Rounding $rounding
    ) {
        $this->orderItemPriceCalculation = $orderItemPriceCalculation;
        $this->rounding = $rounding;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Order $order
     * @return \Shopsys\FrameworkBundle\Model\Order\OrderTotalPrice
     */
    public function getOrderTotalPrice(Order $order): OrderTotalPrice
    {
        $priceWithVat = Money::zero();
        $priceWithoutVat = Money::zero();
        $productPriceWithVat = Money::zero();

        foreach ($order->getItems() as $orderItem) {
            $itemTotalPrice = $this->orderItemPriceCalculation->calculateTotalPrice($orderItem);

            $priceWithVat = $priceWithVat->add($itemTotalPrice->getPriceWithVat());
            $priceWithoutVat = $priceWithoutVat->add($itemTotalPrice->getPriceWithoutVat());

            if ($orderItem->isTypeProduct()) {
                $productPriceWithVat = $productPriceWithVat->add($itemTotalPrice->getPriceWithVat());
            }
        }

        return new OrderTotalPrice($priceWithVat, $priceWithoutVat, $productPriceWithVat);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Payment\Payment $payment
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency $currency
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Price $orderTotalPrice
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Price|null
     */
    public function calculateOrderRoundingPrice(
        Payment $payment,
        Currency $currency,
        Price $orderTotalPrice
    ): ?Price {
        if (!$payment->isCzkRounding() || $currency->getCode() !== Currency::CODE_CZK) {
            return null;
        }

        $priceWithVat = $orderTotalPrice->getPriceWithVat();
        $roundedPriceWithVat = $priceWithVat->round(0);

        $roundingPrice = $this->rounding->roundPriceWithVat($roundedPriceWithVat->subtract($priceWithVat));

        if ($roundingPrice->isZero()) {
            return null;
        }

        return new Price($roundingPrice, $roundingPrice);
    }
}
