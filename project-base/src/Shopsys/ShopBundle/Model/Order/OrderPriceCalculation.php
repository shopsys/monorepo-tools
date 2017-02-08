<?php

namespace SS6\ShopBundle\Model\Order;

use SS6\ShopBundle\Model\Order\Item\OrderItemPriceCalculation;
use SS6\ShopBundle\Model\Order\Item\OrderProduct;
use SS6\ShopBundle\Model\Order\Order;
use SS6\ShopBundle\Model\Order\OrderTotalPrice;
use SS6\ShopBundle\Model\Payment\Payment;
use SS6\ShopBundle\Model\Pricing\Currency\Currency;
use SS6\ShopBundle\Model\Pricing\Price;
use SS6\ShopBundle\Model\Pricing\Rounding;

class OrderPriceCalculation {

	/**
	 * @var \SS6\ShopBundle\Model\Order\Item\OrderItemPriceCalculation
	 */
	private $orderItemPriceCalculation;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Rounding
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
	 * @param \SS6\ShopBundle\Model\Order\Order $order
	 * @return \SS6\ShopBundle\Model\Order\OrderTotalPrice
	 */
	public function getOrderTotalPrice(Order $order) {
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
	 * @param \SS6\ShopBundle\Model\Payment\Payment $payment
	 * @param \SS6\ShopBundle\Model\Pricing\Currency\Currency $currency
	 * @param \SS6\ShopBundle\Model\Pricing\Price $orderTotalPrice
	 * @return \SS6\ShopBundle\Model\Pricing\Price|null
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
