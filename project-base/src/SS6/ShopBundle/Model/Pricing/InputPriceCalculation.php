<?php

namespace SS6\ShopBundle\Model\Pricing;

use SS6\ShopBundle\Model\Pricing\Vat\Vat;

class InputPriceCalculation {

	/**
	 * @param string $basePriceWithVat
	 * @param Vat $vat
	 * @return string
	 */
	public function getInputPriceWithoutVat($basePriceWithVat, Vat $vat) {
		return 100 * $basePriceWithVat / (100 + $vat->getPercent());
	}

	/**
	 * @param string $basePriceWithVat
	 * @return string
	 */
	public function getInputPriceWithVat($basePriceWithVat) {
		return $basePriceWithVat;
	}

}
