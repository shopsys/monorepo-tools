<?php

namespace SS6\ShopBundle\Model\Order\Item;

use SS6\ShopBundle\Model\Order\Item\OrderItem;
use SS6\ShopBundle\Model\Pricing\Price;
use SS6\ShopBundle\Model\Pricing\PriceCalculation;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;
use SS6\ShopBundle\Model\Pricing\Vat\VatData;

class OrderItemPriceCalculation {

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\PriceCalculation
	 */
	private $priceCalculation;

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\PriceCalculation $priceCalculation
	 */
	public function __construct(PriceCalculation $priceCalculation) {
		$this->priceCalculation = $priceCalculation;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Item\OrderItemData $orderItemData
	 * @return string
	 */
	public function calculatePriceWithoutVat(OrderItemData $orderItemData) {
		$vat = new Vat(new VatData('orderItemVat', $orderItemData->vatPercent));
		$vatAmount = $this->priceCalculation->getVatAmountByPriceWithVat($orderItemData->priceWithVat, $vat);

		return $orderItemData->priceWithVat - $vatAmount;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Item\OrderItem $orderItem
	 * @return \SS6\ShopBundle\Model\Pricing\Price
	 */
	public function calculateTotalPrice(OrderItem $orderItem) {
		$vat = new Vat(new VatData('orderItemVat', $orderItem->getVatPercent()));

		$totalPriceWithVat = $orderItem->getPriceWithVat() * $orderItem->getQuantity();
		$totalVatAmount = $this->priceCalculation->getVatAmountByPriceWithVat($totalPriceWithVat, $vat);
		$totalPriceWithoutVat = $totalPriceWithVat - $totalVatAmount;

		return new Price($totalPriceWithoutVat, $totalPriceWithVat);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Item\OrderItem[] $orderItems
	 * @return \SS6\ShopBundle\Model\Pricing\Price[]
	 */
	public function calculateTotalPricesIndexedById($orderItems) {
		$prices = [];

		foreach ($orderItems as $orderItem) {
			if ($orderItem->getId() === null) {
				$message = 'OrderItem must have ID filled';
				throw new \SS6\ShopBundle\Model\Order\Item\Exception\OrderItemHasNoIdException($message);
			}
			$prices[$orderItem->getId()] = $this->calculateTotalPrice($orderItem);
		}

		return $prices;
	}

}
