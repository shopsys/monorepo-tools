<?php

namespace Shopsys\ShopBundle\Model\Pricing;

use Shopsys\ShopBundle\Model\Pricing\Rounding;
use Shopsys\ShopBundle\Model\Pricing\Vat\Vat;

class PriceCalculation {

	/**
	 * @var \Shopsys\ShopBundle\Model\Pricing\Rounding
	 */
	private $rounding;

	public function __construct(Rounding $rounding) {
		$this->rounding = $rounding;
	}

	/**
	 * @param string $priceWithVat
	 * @param \Shopsys\ShopBundle\Model\Pricing\Vat\Vat $vat
	 * @return string
	 */
	public function getVatAmountByPriceWithVat($priceWithVat, Vat $vat) {
		return $this->rounding->roundVatAmount(
			$priceWithVat * $this->getVatCoefficientByPercent($vat->getPercent())
		);
	}

	/**
	 * @param string $vatPercent
	 * @return string
	 */
	public function getVatCoefficientByPercent($vatPercent) {
		$ratio = $vatPercent / (100 + $vatPercent);
		return round($ratio, 4);
	}

	/**
	 * @param string $priceWithoutVat
	 * @param \Shopsys\ShopBundle\Model\Pricing\Vat\Vat
	 * @return string
	 */
	public function applyVatPercent($priceWithoutVat, Vat $vat) {
		return $priceWithoutVat * (100 + $vat->getPercent()) / 100;
	}
}
