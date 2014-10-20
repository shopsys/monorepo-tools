<?php

namespace SS6\ShopBundle\Model\Order;

class OrderTotalPrice {

	/**
	 * @var string
	 */
	private $priceWithVat;

	/**
	 * @var string
	 */
	private $priceWithoutVat;

	/**
	 * @var string
	 */
	private $productPrice;

	/**
	 *
	 * @param string $priceWithVat
	 * @param string $priceWithoutVat
	 * @param string $productPrice
	 */
	public function __construct($priceWithVat, $priceWithoutVat, $productPrice) {
		$this->priceWithVat = $priceWithVat;
		$this->priceWithoutVat = $priceWithoutVat;
		$this->productPrice = $productPrice;
	}

	/**
	 * @return string
	 */
	public function getPriceWithVat() {
		return $this->priceWithVat;
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
	public function getProductPrice() {
		return $this->productPrice;
	}

}
