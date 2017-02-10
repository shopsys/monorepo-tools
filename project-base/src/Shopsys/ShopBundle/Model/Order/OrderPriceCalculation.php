<?php

namespace Shopsys\ShopBundle\Model\Order;

use Shopsys\ShopBundle\Model\Order\Item\OrderItemPriceCalculation;
use Shopsys\ShopBundle\Model\Order\Item\OrderProduct;
use Shopsys\ShopBundle\Model\Order\Order;
use Shopsys\ShopBundle\Model\Order\OrderTotalPrice;
use Shopsys\ShopBundle\Model\Payment\Payment;
use Shopsys\ShopBundle\Model\Pricing\Currency\Currency;
use Shopsys\ShopBundle\Model\Pricing\Price;
use Shopsys\ShopBundle\Model\Pricing\Rounding;

class OrderPriceCalculation
{
    /**
     * @var \Shopsys\ShopBundle\Model\Order\Item\OrderItemPriceCalculation
     */
    private $orderItemPriceCalculation;

    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\Rounding
     */
    private $rounding;

    public function __construct(
        OrderItemPriceCalculation $orderItemPriceCalculation,
        Rounding $rounding
    ) {
        $this->orderItemPriceCalculation = $orderItemPriceCalculation;
        $this->rounding = $rounding;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Order\Order $order
     * @return \Shopsys\ShopBundle\Model\Order\OrderTotalPrice
     */
    public function getOrderTotalPrice(Order $order)
    {
        $priceWithVat = 0;
        $priceWithoutVat = 0;
        $productPriceWithVat = 0;

        foreach ($order->getItems() as $orderItem) {
            $itemTotalPrice = $this->orderItemPriceCalculation->calculateTotalPrice($orderItem);

            $priceWithVat += $itemTotalPrice->getPriceWithVat();
            $priceWithoutVat += $itemTotalPrice->getPriceWithoutVat();

            if ($orderItem instanceof OrderProduct) {
                $productPriceWithVat += $itemTotalPrice->getPriceWithVat();
            }
        }

        return new OrderTotalPrice($priceWithVat, $priceWithoutVat, $productPriceWithVat);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Payment\Payment $payment
     * @param \Shopsys\ShopBundle\Model\Pricing\Currency\Currency $currency
     * @param \Shopsys\ShopBundle\Model\Pricing\Price $orderTotalPrice
     * @return \Shopsys\ShopBundle\Model\Pricing\Price|null
     */
    public function calculateOrderRoundingPrice(
        Payment $payment,
        Currency $currency,
        Price $orderTotalPrice
    ) {
        if (!$payment->isCzkRounding() || $currency->getCode() !== Currency::CODE_CZK) {
            return null;
        }

        $roundingPrice = $this->rounding->roundPriceWithVat(
            round($orderTotalPrice->getPriceWithVat()) - $orderTotalPrice->getPriceWithVat()
        );
        if ($roundingPrice === 0.0) {
            return null;
        }

        return new Price($roundingPrice, $roundingPrice);
    }
}
