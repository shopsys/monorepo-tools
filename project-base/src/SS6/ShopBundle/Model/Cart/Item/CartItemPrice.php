<?php

namespace SS6\ShopBundle\Model\Cart\Item;

class CartItemPrice {

	private $unitPriceWithoutVat;

	private $unitPriceWithVat;

	private $unitPriceVatAmount;

	private $totalPriceWithoutVat;

	private $totalPriceWithVat;

	private $totalPriceVatAmount;

	public function __construct(
		$unitPriceWithoutVat,
		$unitPriceWithVat,
		$unitPriceVatAmount,
		$totalPriceWithoutVat,
		$totalPriceWithVat,
		$totalPriceVatAmount
	) {
		$this->unitPriceWithoutVat = $unitPriceWithoutVat;
		$this->unitPriceWithVat = $unitPriceWithVat;
		$this->unitPriceVatAmount = $unitPriceVatAmount;
		$this->totalPriceWithoutVat = $totalPriceWithoutVat;
		$this->totalPriceWithVat = $totalPriceWithVat;
		$this->totalPriceVatAmount = $totalPriceVatAmount;
	}

	public function getUnitPriceWithoutVat() {
		return $this->unitPriceWithoutVat;
	}

	public function getUnitPriceWithVat() {
		return $this->unitPriceWithVat;
	}

	public function getUnitPriceVatAmount() {
		return $this->unitPriceVatAmount;
	}

	public function getTotalPriceWithoutVat() {
		return $this->totalPriceWithoutVat;
	}

	public function getTotalPriceWithVat() {
		return $this->totalPriceWithVat;
	}

	public function getTotalPriceVatAmount() {
		return $this->totalPriceVatAmount;
	}

}
