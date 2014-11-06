<?php

namespace SS6\ShopBundle\Model\Pricing;

class InputPriceCalculation {

	/**
	 * @param string $basePriceWithVat
	 * @param string $vatPercent
	 * @return string
	 */
	public function getInputPriceWithoutVat($basePriceWithVat, $vatPercent) {
		return 100 * $basePriceWithVat / (100 + $vatPercent);
	}

	/**
	 * @param string $basePriceWithVat
	 * @return string
	 */
	public function getInputPriceWithVat($basePriceWithVat) {
		return $basePriceWithVat;
	}

}
