<?php

namespace SS6\ShopBundle\Model\Cart;

class CartSummary {

	private $quantity;

	private $priceWithoutVat;

	private $priceWithVat;

	public function __construct($quantity, $priceWithoutVat, $priceWithVat) {
		$this->quantity = $quantity;
		$this->priceWithoutVat = $priceWithoutVat;
		$this->priceWithVat = $priceWithVat;
	}

	public function getQuantity() {
		return $this->quantity;
	}

	public function getPriceWithoutVat() {
		return $this->priceWithoutVat;
	}

	public function getPriceWithVat() {
		return $this->priceWithVat;
	}

}
