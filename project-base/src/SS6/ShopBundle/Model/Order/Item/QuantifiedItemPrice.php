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
	 * @return string
	 */
	public function getUnitPriceWithoutVat() {
		return $this->unitPrice->getPriceWithoutVat();
	}

	/**
	 * @return string
	 */
	public function getUnitPriceWithVat() {
		return $this->unitPrice->getPriceWithVat();
	}

	/**
	 * @return string
	 */
	public function getUnitPriceVatAmount() {
		return $this->unitPrice->getVatAmount();
	}

	/**
	 * @return string
	 */
	public function getTotalPriceWithoutVat() {
		return $this->totalPrice->getPriceWithoutVat();
	}

	/**
	 * @return string
	 */
	public function getTotalPriceWithVat() {
		return $this->totalPrice->getPriceWithVat();
	}

	/**
	 * @return string
	 */
	public function getTotalPriceVatAmount() {
		return $this->totalPrice->getVatAmount();
	}

	/**
	 * @return \SS6\ShopBundle\Model\Pricing\Vat\Vat
	 */
	public function getVat() {
		return $this->vat;
	}

}
