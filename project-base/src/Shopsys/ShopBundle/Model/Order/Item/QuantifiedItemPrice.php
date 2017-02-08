<?php

namespace SS6\ShopBundle\Model\Order\Item;

use SS6\ShopBundle\Model\Pricing\Price;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;

class QuantifiedItemPrice {

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Price
	 */
	private $unitPrice;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Price
	 */
	private $totalPrice;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Vat\Vat
	 */
	private $vat;

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Price $unitPrice
	 * @param \SS6\ShopBundle\Model\Pricing\Price $totalPrice
	 * @param \SS6\ShopBundle\Model\Pricing\Vat\Vat $vat
	 */
	public function __construct(
		Price $unitPrice,
		Price $totalPrice,
		Vat $vat
	) {
		$this->unitPrice = $unitPrice;
		$this->totalPrice = $totalPrice;
		$this->vat = $vat;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Pricing\Price
	 */
	public function getUnitPrice() {
		return $this->unitPrice;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Pricing\Price
	 */
	public function getTotalPrice() {
		return $this->totalPrice;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Pricing\Vat\Vat
	 */
	public function getVat() {
		return $this->vat;
	}

}
