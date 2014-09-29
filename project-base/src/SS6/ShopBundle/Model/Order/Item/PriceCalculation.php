<?php

namespace SS6\ShopBundle\Model\Order\Item;

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

}
