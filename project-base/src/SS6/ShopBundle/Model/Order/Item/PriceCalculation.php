<?php

namespace SS6\ShopBundle\Model\Order\Item;

use SS6\ShopBundle\Model\Order\Item\OrderItem;
use SS6\ShopBundle\Model\Pricing\Price;
use SS6\ShopBundle\Model\Pricing\PriceCalculation as GenericPriceCalculation;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;
use SS6\ShopBundle\Model\Pricing\Vat\VatData;

class PriceCalculation {

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\PriceCalculation
	 */
	private $genericPriceCalculation;

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\PriceCalculation $genericPriceCalculation
	 */
	public function __construct(GenericPriceCalculation $genericPriceCalculation) {
		$this->genericPriceCalculation = $genericPriceCalculation;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Item\OrderItemData $orderItemData
	 */
	public function calculatePriceWithoutVat(OrderItemData $orderItemData) {
		$vat = new Vat(new VatData('orderItemVat', $orderItemData->getVatPercent()));

		$vatAmount = $this->genericPriceCalculation->getVatAmountByPriceWithVat($orderItemData->getPriceWithVat(), $vat);
		$priceWithoutVat = $orderItemData->getPriceWithVat() - $vatAmount;
		$orderItemData->setPriceWithoutVat($priceWithoutVat);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Item\OrderItem $orderItem
	 * @return \SS6\ShopBundle\Model\Pricing\Price
	 */
	public function calculateTotalPrice(OrderItem $orderItem) {
		$vat = new Vat(new VatData('orderItemVat', $orderItem->getVatPercent()));

		$totalPriceWithVat = $orderItem->getPriceWithVat() * $orderItem->getQuantity();
		$totalVatAmount = $this->genericPriceCalculation->getVatAmountByPriceWithVat($totalPriceWithVat, $vat);
		$totalPriceWithoutVat = $totalPriceWithVat - $totalVatAmount;

		return new Price(
			$totalPriceWithoutVat,
			$totalPriceWithVat,
			$totalVatAmount
		);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Item\OrderItem[] $orderItems
	 * @return \SS6\ShopBundle\Model\Pricing\Price[]
	 */
	public function calculateTotalPricesIndexedById($orderItems) {
		$prices = array();

		foreach ($orderItems as $orderItem) {
			if ($orderItem->getId() === null) {
				$message = 'OrderItem must have ID filled';
				throw new SS6\ShopBundle\Model\Order\Item\Exception\OrderItemHasNoIdException($message);
			}
			$prices[$orderItem->getId()] = $this->calculateTotalPrice($orderItem);
		}

		return $prices;
	}

}
