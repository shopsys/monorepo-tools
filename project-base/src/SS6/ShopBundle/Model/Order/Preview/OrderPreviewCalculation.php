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
	 * @param float|null $discountPercent
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
		$discountPercent = null
	) {
		$quantifiedItemsPrices = $this->quantifiedProductPriceCalculation->calculatePrices(
			$quantifiedProducts,
			$domainId,
			$user
		);
		$quantifiedItemsDiscounts = $this->quantifiedProductDiscountCalculation->calculateDiscounts(
			$quantifiedItemsPrices,
			$discountPercent
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
			$roundingAmount = $this->calculateRoundingAmount(
				$payment,
				$currency,
				$productsPrice,
				$transportPrice,
				$paymentPrice
			);
		} else {
			$paymentPrice = null;
			$roundingAmount = null;
		}

		$totalPrice = $this->calculateTotalPrice(
			$productsPrice,
			$transportPrice,
			$paymentPrice,
			$roundingAmount
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
			$roundingAmount
		);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Payment\Payment $payment
	 * @param \SS6\ShopBundle\Model\Pricing\Currency\Currency $currency
	 * @param \SS6\ShopBundle\Model\Pricing\Price $productsPrice
	 * @param \SS6\ShopBundle\Model\Pricing\Price|null $transportPrice
	 * @param \SS6\ShopBundle\Model\Pricing\Price|null $paymentPrice
	 * @return string|null
	 */
	private function calculateRoundingAmount(
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

		return $this->orderPriceCalculation->calculateOrderRoundingAmount($payment, $currency, $totalPrice);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Price $productsPrice
	 * @param \SS6\ShopBundle\Model\Pricing\Price|null $transportPrice
	 * @param \SS6\ShopBundle\Model\Pricing\Price|null $paymentPrice
	 * @param string|null $roundingAmount
	 * @return \SS6\ShopBundle\Model\Pricing\Price
	 */
	private function calculateTotalPrice(
		Price $productsPrice,
		Price $transportPrice = null,
		Price $paymentPrice = null,
		$roundingAmount = null
	) {
		$totalPriceWithoutVat = 0;
		$totalPriceWithVat = 0;
		$totalPriceVatAmount = 0;

		$totalPriceWithoutVat += $productsPrice->getPriceWithoutVat();
		$totalPriceWithVat += $productsPrice->getPriceWithVat();
		$totalPriceVatAmount += $productsPrice->getVatAmount();

		if ($transportPrice !== null) {
			$totalPriceWithoutVat += $transportPrice->getPriceWithoutVat();
			$totalPriceWithVat += $transportPrice->getPriceWithVat();
			$totalPriceVatAmount += $transportPrice->getVatAmount();
		}

		if ($paymentPrice !== null) {
			$totalPriceWithoutVat += $paymentPrice->getPriceWithoutVat();
			$totalPriceWithVat += $paymentPrice->getPriceWithVat();
			$totalPriceVatAmount += $paymentPrice->getVatAmount();
		}

		if ($roundingAmount !== null) {
			$totalPriceWithoutVat += $roundingAmount;
			$totalPriceWithVat += $roundingAmount;
		}

		return new Price(
			$totalPriceWithoutVat,
			$totalPriceWithVat,
			$totalPriceVatAmount
		);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Item\QuantifiedItemPrice[] $quantifiedItemsPrices
	 * @param \SS6\ShopBundle\Model\Pricing\Price[] $quantifiedItemsDiscounts
	 * @return \SS6\ShopBundle\Model\Pricing\Price
	 */
	private function getProductsPrice(array $quantifiedItemsPrices, array $quantifiedItemsDiscounts) {
		$productsPriceWithoutVat = 0;
		$productsPriceWithVat = 0;
		$productsPriceVatAmount = 0;

		foreach ($quantifiedItemsPrices as $quantifiedItemPrice) {
			/* @var $quantifiedItemPrice \SS6\ShopBundle\Model\Order\Item\QuantifiedItemPrice */
			$productsPriceWithoutVat += $quantifiedItemPrice->getTotalPriceWithoutVat();
			$productsPriceWithVat += $quantifiedItemPrice->getTotalPriceWithVat();
			$productsPriceVatAmount += $quantifiedItemPrice->getTotalPriceVatAmount();
		}

		foreach ($quantifiedItemsDiscounts as $discount) {
			if ($discount !== null) {
				/* @var $discount \SS6\ShopBundle\Model\Pricing\Price */
				$productsPriceWithoutVat -= $discount->getPriceWithoutVat();
				$productsPriceWithVat -= $discount->getPriceWithVat();
				$productsPriceVatAmount -= $discount->getVatAmount();
			}
		}

		return new Price(
			$productsPriceWithoutVat,
			$productsPriceWithVat,
			$productsPriceVatAmount
		);
	}

}
