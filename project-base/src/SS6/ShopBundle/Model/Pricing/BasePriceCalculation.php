<?php

namespace SS6\ShopBundle\Model\Pricing;

use SS6\ShopBundle\Model\Pricing\PriceCalculation;
use SS6\ShopBundle\Model\Pricing\Rounding;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;

class BasePriceCalculation {

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\PriceCalculation
	 */
	private $priceCalculation;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Rounding
	 */
	private $rounding;

	/**
	 * @var string
	 */
	private $inputPrice;

	/**
	 * @var int
	 */
	private $inputPriceType;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Vat\Vat
	 */
	private $vat;

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\PriceCalculation $priceCalculation
	 * @param \SS6\ShopBundle\Model\Pricing\Rounding $rounding
	 */
	public function __construct(PriceCalculation $priceCalculation, Rounding $rounding) {
		$this->priceCalculation = $priceCalculation;
		$this->rounding = $rounding;
	}

	/**
	 * @param string $inputPrice
	 * @param int $inputPriceType
	 * @param \SS6\ShopBundle\Model\Pricing\Vat\Vat $vat
	 * @return \SS6\ShopBundle\Model\Pricing\Price
	 */
	public function calculatePrice($inputPrice, $inputPriceType, Vat $vat) {
		$this->inputPrice = $inputPrice;
		$this->inputPriceType = $inputPriceType;
		$this->vat = $vat;

		$basePriceWithVat = $this->getBasePriceWithVat();
		$vatAmount = $this->priceCalculation->getVatAmountByPriceWithVat($basePriceWithVat, $this->vat);
		$basePriceWithoutVat = $this->rounding->roundPriceWithoutVat($basePriceWithVat - $vatAmount);

		return new Price(
			$basePriceWithoutVat,
			$basePriceWithVat,
			$vatAmount
		);
	}

	/**
	 * @return string
	 */
	private function getBasePriceWithVat() {
		switch ($this->inputPriceType) {
			case PricingSetting::INPUT_PRICE_TYPE_WITH_VAT:
				return $this->rounding->roundPriceWithVat($this->inputPrice);

			case PricingSetting::INPUT_PRICE_TYPE_WITHOUT_VAT:
				return $this->rounding->roundPriceWithVat($this->priceCalculation->applyVatPercent($this->inputPrice, $this->vat));

			default:
				throw new \SS6\ShopBundle\Model\Pricing\Exception\InvalidInputPriceTypeException();
		}
	}

}
