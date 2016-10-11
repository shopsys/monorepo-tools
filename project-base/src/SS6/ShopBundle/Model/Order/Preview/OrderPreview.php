<?php

namespace SS6\ShopBundle\Model\Order\Preview;

use SS6\ShopBundle\Model\Payment\Payment;
use SS6\ShopBundle\Model\Pricing\Price;
use SS6\ShopBundle\Model\Transport\Transport;

class OrderPreview {

	/**
	 * @var \SS6\ShopBundle\Model\Order\Item\QuantifiedProduct[quantifiedProductIndex]
	 */
	private $quantifiedProducts;

	/**
	 * @var \SS6\ShopBundle\Model\Order\Item\QuantifiedItemPrice[quantifiedItemIndex]
	 */
	private $quantifiedItemsPrices;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Price[quantifiedItemIndex]
	 */
	private $quantifiedItemsDiscounts;

	/**
	 * @var \SS6\ShopBundle\Model\Transport\Transport|null
	 */
	private $transport;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Price|null
	 */
	private $transportPrice;

	/**
	 * @var \SS6\ShopBundle\Model\Payment\Payment|null
	 */
	private $payment;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Price|null
	 */
	private $paymentPrice;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Price
	 */
	private $totalPrice;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Price
	 */
	private $productsPrice;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Price|null
	 */
	private $roundingPrice;

	/**
	 * @var float|null
	 */
	private $discountPercent;

	/**
	 * @param array $quantifiedProducts
	 * @param array $quantifiedItemsPrices
	 * @param array $quantifiedItemsDiscounts
	 * @param \SS6\ShopBundle\Model\Pricing\Price $productsPrice
	 * @param \SS6\ShopBundle\Model\Pricing\Price $totalPrice
	 * @param \SS6\ShopBundle\Model\Transport\Transport|null $transport
	 * @param \SS6\ShopBundle\Model\Pricing\Price|null $transportPrice
	 * @param \SS6\ShopBundle\Model\Payment\Payment|null $payment
	 * @param \SS6\ShopBundle\Model\Pricing\Price|null $paymentPrice
	 * @param \SS6\ShopBundle\Model\Pricing\Price|null $roundingPrice
	 * @param float|null $discountPercent
	 */
	public function __construct(
		array $quantifiedProducts,
		array $quantifiedItemsPrices,
		array $quantifiedItemsDiscounts,
		Price $productsPrice,
		Price $totalPrice,
		Transport $transport = null,
		Price $transportPrice = null,
		Payment $payment = null,
		Price $paymentPrice = null,
		Price $roundingPrice = null,
		$discountPercent = null
	) {
		$this->quantifiedProducts = $quantifiedProducts;
		$this->quantifiedItemsPrices = $quantifiedItemsPrices;
		$this->quantifiedItemsDiscounts = $quantifiedItemsDiscounts;
		$this->productsPrice = $productsPrice;
		$this->totalPrice = $totalPrice;
		$this->transport = $transport;
		$this->transportPrice = $transportPrice;
		$this->payment = $payment;
		$this->paymentPrice = $paymentPrice;
		$this->roundingPrice = $roundingPrice;
		$this->discountPercent = $discountPercent;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Order\Item\QuantifiedProduct[quantifiedProductIndex]
	 */
	public function getQuantifiedProducts() {
		return $this->quantifiedProducts;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Order\Item\QuantifiedItemPrice[quantifiedItemIndex]
	 */
	public function getQuantifiedItemsPrices() {
		return $this->quantifiedItemsPrices;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Pricing\Price[quantifiedItemIndex]
	 */
	public function getQuantifiedItemsDiscounts() {
		return $this->quantifiedItemsDiscounts;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Transport\Transport|null
	 */
	public function getTransport() {
		return $this->transport;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Pricing\Price|null
	 */
	public function getTransportPrice() {
		return $this->transportPrice;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Payment\Payment|null
	 */
	public function getPayment() {
		return $this->payment;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Pricing\Price|null
	 */
	public function getPaymentPrice() {
		return $this->paymentPrice;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Pricing\Price
	 */
	public function getTotalPrice() {
		return $this->totalPrice;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Pricing\Price
	 */
	public function getProductsPrice() {
		return $this->productsPrice;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Pricing\Price|null
	 */
	public function getRoundingPrice() {
		return $this->roundingPrice;
	}

	/**
	 * @return float|null
	 */
	public function getDiscountPercent() {
		return $this->discountPercent;
	}

}
