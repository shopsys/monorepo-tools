<?php

namespace SS6\ShopBundle\Model\Product\Pricing;

use SS6\ShopBundle\Model\Order\Item\QuantifiedItem;
use SS6\ShopBundle\Model\Order\Item\QuantifiedItemPrice;
use SS6\ShopBundle\Model\Pricing\Rounding;
use SS6\ShopBundle\Model\Product\Pricing\ProductPriceCalculationForUser;
use SS6\ShopBundle\Model\Product\Product;

class QuantifiedProductPriceCalculation {

	/**
	 * @var \SS6\ShopBundle\Model\Product\Pricing\ProductPriceCalculationForUser
	 */
	private $productPriceCalculationForUser;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Rounding
	 */
	private $rounding;

	/**
	 * @var \SS6\ShopBundle\Model\Order\Item\QuantifiedItem
	 */
	private $quantifiedItem;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Product
	 */
	private $product;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Price
	 */
	private $productPrice;

	/**
	 * @param \SS6\ShopBundle\Model\Product\Pricing\ProductPriceCalculationForUser $productPriceCalculationForUser
	 * @param \SS6\ShopBundle\Model\Pricing\Rounding$rounding
	 */
	public function __construct(ProductPriceCalculationForUser $productPriceCalculationForUser, Rounding $rounding) {
		$this->productPriceCalculationForUser = $productPriceCalculationForUser;
		$this->rounding = $rounding;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Item\QuantifiedItem $quantifiedItem
	 * @return \SS6\ShopBundle\Model\Order\Item\QuantifiedItemPrice
	 */
	public function calculatePrice(QuantifiedItem $quantifiedItem) {
		$product = $quantifiedItem->getItem();
		if (!$product instanceof Product) {
			$message = 'Object "' . get_class($product) . '" is not valid for QuantifiedProductPriceCalculation.';
			throw new \SS6\ShopBundle\Model\Order\Item\Exception\InvalidQuantifiedItemException($message);
		}

		$this->quantifiedItem = $quantifiedItem;
		$this->product = $product;
		$this->productPrice = $this->productPriceCalculationForUser->calculatePriceForCurrentUser($product);

		$quantifiedItemPrice = new QuantifiedItemPrice(
			$this->productPrice->getPriceWithoutVat(),
			$this->productPrice->getPriceWithVat(),
			$this->productPrice->getVatAmount(),
			$this->getTotalPriceWithoutVat(),
			$this->getTotalPriceWithVat(),
			$this->getTotalPriceVatAmount()
		);

		return $quantifiedItemPrice;
	}

	/**
	 * @return string
	 */
	private function getTotalPriceWithoutVat() {
		return $this->getTotalPriceWithVat() - $this->getTotalPriceVatAmount();
	}

	/**
	 * @return string
	 */
	private function getTotalPriceWithVat() {
		return $this->productPrice->getPriceWithVat() * $this->quantifiedItem->getQuantity();
	}

	/**
	 * @return string
	 */
	private function getTotalPriceVatAmount() {
		return $this->rounding->roundVatAmount(
			$this->getTotalPriceWithVat() * $this->product->getVat()->getCoefficient()
		);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Item\QuantifiedItem[quantifiedItemIndex] $quantifiedItems
	 * @return \SS6\ShopBundle\Model\Order\Item\QuantifiedItemPrice[quantifiedItemIndex]
	 */
	public function calculatePrices(array $quantifiedItems) {
		$quantifiedItemsPrices = [];
		foreach ($quantifiedItems as $index => $quantifiedItem) {
			$quantifiedItemsPrices[$index] = $this->calculatePrice($quantifiedItem);
		}

		return $quantifiedItemsPrices;
	}

}
