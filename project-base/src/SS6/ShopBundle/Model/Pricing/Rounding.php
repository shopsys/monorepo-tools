<?php

namespace SS6\ShopBundle\Model\Pricing;

class Rounding {

	/**
	 * @param string $priceWithVat
	 * @return string
	 */
	public function roundPriceWithVat($priceWithVat) {
		return round($priceWithVat, 0);
	}

	/**
	 * @param string $priceWithoutVat
	 * @return string
	 */
	public function roundPriceWithoutVat($priceWithoutVat) {
		return round($priceWithoutVat, 2);
	}

	/**
	 * @param string $vatAmount
	 * @return string
	 */
	public function roundVatAmount($vatAmount) {
		return round($vatAmount, 2);
	}
}
