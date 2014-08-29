<?php

namespace SS6\ShopBundle\Model\Cart\Item;

class CartItemPrice {

	/**
	 * @var string
	 */
	private $unitPriceWithoutVat;

	/**
	 * @var string
	 */
	private $unitPriceWithVat;

	/**
	 * @var string
	 */
	private $unitPriceVatAmount;

	/**
	 * @var string
	 */
	private $totalPriceWithoutVat;

	/**
	 * @var string
	 */
	private $totalPriceWithVat;

	/**
	 * @var string
	 */
	private $totalPriceVatAmount;

	/**
	 * @param string $unitPriceWithoutVat
	 * @param string $unitPriceWithVat
	 * @param string $unitPriceVatAmount
	 * @param string $totalPriceWithoutVat
	 * @param string $totalPriceWithVat
	 * @param string $totalPriceVatAmount
	 */
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

	/**
	 * @return string
	 */
	public function getUnitPriceWithoutVat() {
		return $this->unitPriceWithoutVat;
	}

	/**
	 * @return string
	 */
	public function getUnitPriceWithVat() {
		return $this->unitPriceWithVat;
	}

	/**
	 * @return string
	 */
	public function getUnitPriceVatAmount() {
		return $this->unitPriceVatAmount;
	}

	/**
	 * @return string
	 */
	public function getTotalPriceWithoutVat() {
		return $this->totalPriceWithoutVat;
	}

	/**
	 * @return string
	 */
	public function getTotalPriceWithVat() {
		return $this->totalPriceWithVat;
	}

	/**
	 * @return string
	 */
	public function getTotalPriceVatAmount() {
		return $this->totalPriceVatAmount;
	}

}
