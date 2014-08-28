<?php

namespace SS6\ShopBundle\Model\Pricing;

use SS6\ShopBundle\Model\Pricing\Vat\Vat;

class PriceCalculation {

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
	 * @param string $inputPrice
	 * @param int $inputPriceType
	 * @param \SS6\ShopBundle\Model\Pricing\Vat\Vat $vat
	 * @return \SS6\ShopBundle\Model\Pricing\Price
	 */
	public function calculatePrice($inputPrice, $inputPriceType, Vat $vat) {
		$this->inputPrice = $inputPrice;
		$this->inputPriceType = $inputPriceType;
		$this->vat = $vat;

		return new Price(
			$this->getBasePriceWithoutVat(),
			$this->getBasePriceWithVat(),
			$this->getBasePriceVatAmount()
		);
	}

	/**
	 * @return string
	 */
	private function getBasePriceWithoutVat() {
		return $this->getBasePriceWithVat() - $this->getBasePriceVatAmount();
	}

	/**
	 * @return string
	 * @throws \SS6\ShopBundle\Model\Pricing\Exception\InvalidInputPriceTypeException
	 */
	private function getBasePriceWithVat() {
		switch ($this->inputPriceType) {
			case PricingSetting::INPUT_PRICE_TYPE_WITH_VAT:
				return $this->roundPriceWithVat($this->inputPrice);

			case PricingSetting::INPUT_PRICE_TYPE_WITHOUT_VAT:
				return $this->roundPriceWithVat($this->applyVatPercent($this->inputPrice));

			default:
				throw new \SS6\ShopBundle\Model\Pricing\Exception\InvalidInputPriceTypeException();
		}
	}

	/**
	 * @return string
	 */
	private function getBasePriceVatAmount() {
		return $this->roundVatAmount($this->getBasePriceWithVat() * $this->vat->getCoefficient());
	}

	/**
	 * @param string $price
	 * @return string
	 */
	private function roundPriceWithVat($price) {
		return round($price, 0);
	}

	/**
	 * @param string $vatAmount
	 * @return string
	 */
	private function roundVatAmount($vatAmount) {
		return round($vatAmount, 2);
	}

	/**
	 * @param string $price
	 * @return string
	 */
	private function applyVatPercent($price) {
		return $price * (100 + $this->vat->getPercent()) / 100;
	}

}
