<?php

namespace SS6\ShopBundle\Model\Order\Preview;

use SS6\ShopBundle\Model\Customer\User;
use SS6\ShopBundle\Model\Order\OrderPriceCalculation;
use SS6\ShopBundle\Model\Payment\Payment;
use SS6\ShopBundle\Model\Payment\PaymentPriceCalculation;
use SS6\ShopBundle\Model\Pricing\Currency\Currency;
use SS6\ShopBundle\Model\Pricing\Price;
use SS6\ShopBundle\Model\Product\Pricing\QuantifiedProductDiscountCalculation;
use SS6\ShopBundle\Model\Product\Pricing\QuantifiedProductPriceCalculation;
use SS6\ShopBundle\Model\Transport\Transport;
use SS6\ShopBundle\Model\Transport\TransportPriceCalculation;

class OrderPreviewCalculation {

	/**
	 * @var \SS6\ShopBundle\Model\Product\Pricing\QuantifiedProductPriceCalculation
	 */
	private $quantifiedProductPriceCalculation;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Pricing\QuantifiedProductDiscountCalculation
	 */
	private $quantifiedProductDiscountCalculation;

	/**
	 * @var \SS6\ShopBundle\Model\Transport\TransportPriceCalculation
	 */
	private $transportPriceCalculation;

	/**
	 * @var \SS6\ShopBundle\Model\Payment\PaymentPriceCalculation
	 */
	private $paymentPriceCalculation;

	/**
	 * @var \SS6\ShopBundle\Model\Order\OrderPriceCalculation
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
	 * @param \SS6\ShopBundle\Model\Pricing\Currency\Currency $currency
	 * @param int $domainId
	 * @param \SS6\ShopBundle\Model\Order\Item\QuantifiedProduct[] $quantifiedProducts
	 * @param \SS6\ShopBundle\Model\Transport\Transport|null $transport
	 * @param \SS6\ShopBundle\Model\Payment\Payment|null $payment
	 * @param \SS6\ShopBundle\Model\Customer\User|null $user
	 * @param float|null $promoCodeDiscountPercent
	 * @return \SS6\ShopBundle\Model\Order\Preview\OrderPreview
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
	 * @param \SS6\ShopBundle\Model\Payment\Payment $payment
	 * @param \SS6\ShopBundle\Model\Pricing\Currency\Currency $currency
	 * @param \SS6\ShopBundle\Model\Pricing\Price $productsPrice
	 * @param \SS6\ShopBundle\Model\Pricing\Price|null $transportPrice
	 * @param \SS6\ShopBundle\Model\Pricing\Price|null $paymentPrice
	 * @return \SS6\ShopBundle\Model\Pricing\Price|null
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
	 * @param \SS6\ShopBundle\Model\Pricing\Price $productsPrice
	 * @param \SS6\ShopBundle\Model\Pricing\Price|null $transportPrice
	 * @param \SS6\ShopBundle\Model\Pricing\Price|null $paymentPrice
	 * @param \SS6\ShopBundle\Model\Pricing\Price|null $roundingPrice
	 * @return \SS6\ShopBundle\Model\Pricing\Price
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
	 * @param \SS6\ShopBundle\Model\Order\Item\QuantifiedItemPrice[] $quantifiedItemsPrices
	 * @param \SS6\ShopBundle\Model\Pricing\Price[] $quantifiedItemsDiscounts
	 * @return \SS6\ShopBundle\Model\Pricing\Price
	 */
	private function getProductsPrice(array $quantifiedItemsPrices, array $quantifiedItemsDiscounts) {
		$finalPrice = new Price(0, 0);

		foreach ($quantifiedItemsPrices as $quantifiedItemPrice) {
			/* @var $quantifiedItemPrice \SS6\ShopBundle\Model\Order\Item\QuantifiedItemPrice */
			$finalPrice = $finalPrice->add($quantifiedItemPrice->getTotalPrice());
		}

		foreach ($quantifiedItemsDiscounts as $discount) {
			if ($discount !== null) {
				/* @var $discount \SS6\ShopBundle\Model\Pricing\Price */
				$finalPrice = $finalPrice->subtract($discount);
			}
		}

		return $finalPrice;
	}

}
