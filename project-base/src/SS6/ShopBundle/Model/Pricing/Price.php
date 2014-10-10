<?php

namespace SS6\ShopBundle\Model\Pricing;

class Price {

	/**
	 * @var string
	 */
	private $priceWithoutVat;

	/**
	 * @var string
	 */
	private $priceWithVat;

	/**
	 * @var string
	 */
	private $vatAmount;

	/**
	 * @param string $priceWithoutVat
	 * @param string $priceWithVat
	 * @param string $vatAmount
	 */
	public function __construct($priceWithoutVat, $priceWithVat, $vatAmount) {
		$this->priceWithoutVat = $priceWithoutVat;
		$this->priceWithVat = $priceWithVat;
		$this->vatAmount = $vatAmount;
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

	/**
	 * @return string
	 */
	public function getVatAmount() {
		return $this->vatAmount;
	}

}
