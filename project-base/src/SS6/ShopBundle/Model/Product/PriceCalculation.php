<?php

namespace SS6\ShopBundle\Model\Product;

use SS6\ShopBundle\Model\Pricing\Price;
use SS6\ShopBundle\Model\Setting\Setting3;

class PriceCalculation {

	/**
	 * @var string
	 */
	private $inputPrice;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Vat
	 */
	private $vat;

	/**
	 * @param \SS6\ShopBundle\Model\Setting\Setting3 $setting
	 */
	public function __construct(Setting3 $setting) {
		$this->setting = $setting;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @return \SS6\ShopBundle\Model\Pricing\Price
	 */
	public function calculatePrice(Product $product) {
		$this->inputPrice = $product->getPrice();
		$this->vat = $product->getVat();

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
		$inputPriceType = $this->setting->get(Setting3::INPUT_PRICE_TYPE);

		switch ($inputPriceType) {
			case Setting3::INPUT_PRICE_TYPE_WITH_VAT:
				return $this->roundPriceWithVat($this->inputPrice);

			case Setting3::INPUT_PRICE_TYPE_WITHOUT_VAT:
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
