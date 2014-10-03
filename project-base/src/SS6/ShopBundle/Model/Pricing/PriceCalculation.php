<?php

namespace SS6\ShopBundle\Model\Pricing;

use SS6\ShopBundle\Model\Pricing\Rounding;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;

class PriceCalculation {

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Rounding
	 */
	private $rounding;

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Rounding $rounding
	 */
	public function __construct(Rounding $rounding) {
		$this->rounding = $rounding;
	}

	/**
	 * @return string
	 */
	public function getVatAmountByPriceWithVat($priceWithoutVat, Vat $vat) {
		return $this->rounding->roundVatAmount($priceWithoutVat * $vat->getCoefficient());
	}

	/**
	 * @param string $priceWithoutVat
	 * @param \SS6\ShopBundle\Model\Pricing\Vat\Vat
	 * @return string
	 */
	public function applyVatPercent($priceWithoutVat, Vat $vat) {
		return $priceWithoutVat * (100 + $vat->getPercent()) / 100;
	}
}
