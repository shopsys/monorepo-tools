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
	public function calculateBasePrice($inputPrice, $inputPriceType, Vat $vat) {
		$basePriceWithVat = $this->getBasePriceWithVat($inputPrice, $inputPriceType, $vat);
		$vatAmount = $this->priceCalculation->getVatAmountByPriceWithVat($basePriceWithVat, $vat);
		$basePriceWithoutVat = $this->rounding->roundPriceWithoutVat($basePriceWithVat - $vatAmount);

		return new Price($basePriceWithoutVat, $basePriceWithVat);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Price $price
	 * @param Vat $vat
	 * @param string[] $coefficients
	 * @return \SS6\ShopBundle\Model\Pricing\Price
	 */
	public function applyCoefficients(Price $price, Vat $vat, $coefficients) {
		$priceWithVatBeforeRounding = $price->getPriceWithVat();
		foreach ($coefficients as $coefficient) {
			$priceWithVatBeforeRounding *= $coefficient;
		}
		$priceWithVat = $this->rounding->roundPriceWithVat($priceWithVatBeforeRounding);
		$vatAmount = $this->priceCalculation->getVatAmountByPriceWithVat($priceWithVat, $vat);
		$priceWithoutVat = $this->rounding->roundPriceWithoutVat($priceWithVat - $vatAmount);

		return new Price($priceWithoutVat, $priceWithVat);
	}

	/**
	 * @param string $inputPrice
	 * @param int $inputPriceType
	 * @param Vat $vat
	 * @return string
	 */
	private function getBasePriceWithVat($inputPrice, $inputPriceType, Vat $vat) {
		switch ($inputPriceType) {
			case PricingSetting::INPUT_PRICE_TYPE_WITH_VAT:
				return $this->rounding->roundPriceWithVat($inputPrice);

			case PricingSetting::INPUT_PRICE_TYPE_WITHOUT_VAT:
				return $this->rounding->roundPriceWithVat($this->priceCalculation->applyVatPercent($inputPrice, $vat));

			default:
				throw new \SS6\ShopBundle\Model\Pricing\Exception\InvalidInputPriceTypeException();
		}
	}

}
