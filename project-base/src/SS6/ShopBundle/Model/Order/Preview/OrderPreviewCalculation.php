<?php

namespace SS6\ShopBundle\Model\Order\Preview;

use SS6\ShopBundle\Model\Customer\User;
use SS6\ShopBundle\Model\Payment\Payment;
use SS6\ShopBundle\Model\Payment\PaymentPriceCalculation;
use SS6\ShopBundle\Model\Pricing\Currency\Currency;
use SS6\ShopBundle\Model\Pricing\Price;
use SS6\ShopBundle\Model\Product\Pricing\QuantifiedProductPriceCalculation;
use SS6\ShopBundle\Model\Transport\Transport;
use SS6\ShopBundle\Model\Transport\TransportPriceCalculation;

class OrderPreviewCalculation {

	/**
	 * @var \SS6\ShopBundle\Model\Product\Pricing\QuantifiedProductPriceCalculation
	 */
	private $quantifiedProductPriceCalculation;

	/**
	 * @var \SS6\ShopBundle\Model\Transport\TransportPriceCalculation
	 */
	private $transportPriceCalculation;

	/**
	 * @var \SS6\ShopBundle\Model\Payment\PaymentPriceCalculation
	 */
	private $paymentPriceCalculation;

	/**
	 * @param \SS6\ShopBundle\Model\Product\Pricing\QuantifiedProductPriceCalculation $quantifiedProductPriceCalculation
	 * @param \SS6\ShopBundle\Model\Transport\TransportPriceCalculation $transportPriceCalculation
	 * @param \SS6\ShopBundle\Model\Payment\PaymentPriceCalculation $paymentPriceCalculation
	 */
	public function __construct(
		QuantifiedProductPriceCalculation $quantifiedProductPriceCalculation,
		TransportPriceCalculation $transportPriceCalculation,
		PaymentPriceCalculation $paymentPriceCalculation
	) {
		$this->quantifiedProductPriceCalculation = $quantifiedProductPriceCalculation;
		$this->transportPriceCalculation = $transportPriceCalculation;
		$this->paymentPriceCalculation = $paymentPriceCalculation;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Currency\Currency $currency
	 * @param int $domainId
	 * @param \SS6\ShopBundle\Model\Order\Item\QuantifiedItem[] $quantifiedItems
	 * @param \SS6\ShopBundle\Model\Transport\Transport|null $transport
	 * @param \SS6\ShopBundle\Model\Payment\Payment|null $payment
	 * @param \SS6\ShopBundle\Model\Customer\User|null $user
	 * @return \SS6\ShopBundle\Model\Order\Preview\OrderPreview
	 */
	public function calculatePreview(
		Currency $currency,
		$domainId,
		array $quantifiedItems,
		Transport $transport = null,
		Payment $payment = null,
		User $user = null
	) {
		$quantifiedItemsPrices = $this->quantifiedProductPriceCalculation->calculatePrices(
			$quantifiedItems,
			$domainId,
			$user
		);

		if ($transport !== null) {
			$transportPrice = $this->transportPriceCalculation->calculatePrice($transport, $currency);
		} else {
			$transportPrice = null;
		}

		if ($payment !== null) {
			$paymentPrice = $this->paymentPriceCalculation->calculatePrice($payment, $currency);
		} else {
			$paymentPrice = null;
		}

		$totalPrice = $this->calculateTotalPrice(
			$quantifiedItemsPrices,
			$transportPrice,
			$paymentPrice
		);

		return new OrderPreview(
			$quantifiedItems,
			$quantifiedItemsPrices,
			$totalPrice->getPriceWithoutVat(),
			$totalPrice->getPriceWithVat(),
			$totalPrice->getVatAmount(),
			$transport,
			$transportPrice,
			$payment,
			$paymentPrice
		);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Item\QuantifiedItemPrice[] $quantifiedItemsPrices
	 * @param \SS6\ShopBundle\Model\Pricing\Price|null $transportPrice
	 * @param \SS6\ShopBundle\Model\Pricing\Price|null $paymentPrice
	 * @return \SS6\ShopBundle\Model\Pricing\Price
	 */
	private function calculateTotalPrice(
		array $quantifiedItemsPrices,
		Price $transportPrice = null,
		Price $paymentPrice = null
	) {
		$totalPriceWithoutVat = 0;
		$totalPriceWithVat = 0;
		$totalPriceVatAmount = 0;

		foreach ($quantifiedItemsPrices as $quantifiedItemPrice) {
			/* @var $quantifiedItemPrice \SS6\ShopBundle\Model\Order\Item\QuantifiedItemPrice */
			$totalPriceWithoutVat += $quantifiedItemPrice->getTotalPriceWithoutVat();
			$totalPriceWithVat += $quantifiedItemPrice->getTotalPriceWithVat();
			$totalPriceVatAmount += $quantifiedItemPrice->getTotalPriceVatAmount();
		}

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

		return new Price(
			$totalPriceWithoutVat,
			$totalPriceWithVat,
			$totalPriceVatAmount
		);
	}

}
