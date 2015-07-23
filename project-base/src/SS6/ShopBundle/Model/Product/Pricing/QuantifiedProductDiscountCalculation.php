<?php

namespace SS6\ShopBundle\Model\Product\Pricing;

use SS6\ShopBundle\Model\Order\Item\QuantifiedItemPrice;
use SS6\ShopBundle\Model\Pricing\Price;
use SS6\ShopBundle\Model\Pricing\PriceCalculation;
use SS6\ShopBundle\Model\Pricing\Rounding;

class QuantifiedProductDiscountCalculation {

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\PriceCalculation
	 */
	private $priceCalulation;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Rounding
	 */
	private $rounding;

	public function __construct(
		PriceCalculation $priceCalulation,
		Rounding $rounding
	) {
		$this->priceCalulation = $priceCalulation;
		$this->rounding = $rounding;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Item\QuantifiedItem $quantifiedItemPrice
	 * @param float $discountPercent
	 * @return \SS6\ShopBundle\Model\Pricing\Price
	 */
	private function calculateDiscount(QuantifiedItemPrice $quantifiedItemPrice, $discountPercent) {
		$vat = $quantifiedItemPrice->getVat();
		$priceWithVat = $this->rounding->roundPriceWithVat(
			$quantifiedItemPrice->getTotalPriceWithVat() * $discountPercent / 100
		);
		$priceVatAmount = $this->priceCalulation->getVatAmountByPriceWithVat($priceWithVat, $vat);
		$priceWithoutVat = $priceWithVat - $priceVatAmount;

		return new Price($priceWithoutVat, $priceWithVat, $priceVatAmount);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Item\QuantifiedItem[quantifiedItemIndex] $quantifiedItemsPrices
	 * @param float|null $discountPercent
	 * @return \SS6\ShopBundle\Model\Pricing\Price[quantifiedItemIndex]
	 */
	public function calculateDiscounts(array $quantifiedItemsPrices, $discountPercent) {
		$quantifiedItemsDiscounts = [];
		foreach ($quantifiedItemsPrices as $index => $quantifiedItemPrice) {
			if ($discountPercent === null) {
				$quantifiedItemsDiscounts[$index] = null;
			} else {
				$quantifiedItemsDiscounts[$index] = $this->calculateDiscount($quantifiedItemPrice, $discountPercent);
			}
		}

		return $quantifiedItemsDiscounts;
	}

}
