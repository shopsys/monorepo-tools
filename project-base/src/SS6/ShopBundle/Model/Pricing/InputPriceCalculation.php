<?php

namespace SS6\ShopBundle\Model\Pricing;

use SS6\ShopBundle\Model\Pricing\Vat\Vat;

class InputPriceCalculation {

	public function getInputPriceWithoutVat($basePriceWithVat, Vat $vat) {
		return 100 * $basePriceWithVat / (100 + $vat->getPercent());
	}

	public function getInputPriceWithVat($basePriceWithVat) {
		return $basePriceWithVat;
	}

}
