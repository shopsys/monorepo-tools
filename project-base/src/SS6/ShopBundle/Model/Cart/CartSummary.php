<?php

namespace SS6\ShopBundle\Model\Cart;

class CartSummary {

	/**
	 * @var int
	 */
	private $quantity;

	/**
	 * @var string
	 */
	private $priceWithoutVat;

	/**
	 * @var string
	 */
	private $priceWithVat;

	/**
	 * @param int $quantity
	 * @param string $priceWithoutVat
	 * @param string $priceWithVat
	 */
	public function __construct($quantity, $priceWithoutVat, $priceWithVat) {
		$this->quantity = $quantity;
		$this->priceWithoutVat = $priceWithoutVat;
		$this->priceWithVat = $priceWithVat;
	}

	/**
	 * @return int
	 */
	public function getQuantity() {
		return $this->quantity;
	}

	/**
	 * @return string
	 */
	public function getPriceWithoutVat() {
		return $this->priceWithoutVat;
	}

	/**
	 * @return string
	 */
	public function getPriceWithVat() {
		return $this->priceWithVat;
	}

}
