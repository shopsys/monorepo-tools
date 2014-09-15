<?php

namespace SS6\ShopBundle\Twig;

use SS6\ShopBundle\Model\Setting\Setting;
use Twig_Extension;
use Twig_SimpleFunction;

class InputPriceLabelExtension extends Twig_Extension {

	/**
	 * @var \SS6\ShopBundle\Model\Setting\Setting
	 */
	private $setting;

	/**
	 * @param \SS6\ShopBundle\Model\Setting\Setting $setting
	 */
	public function __construct(Setting $setting) {
		$this->setting = $setting;
	}

	/**
	 * @return array
	 */
	public function getFunctions() {
		return array(
			new Twig_SimpleFunction('inputPriceLabel', array($this, 'getInputPriceLabel')),
		);
	}

	/**
	 * @return string
	 * @throws \SS6\ShopBundle\Model\Pricing\Exception\InvalidInputPriceTypeException
	 */
	public function getInputPriceLabel() {
		$inputPriceType = $this->setting->get(Setting::INPUT_PRICE_TYPE);

		switch ($inputPriceType) {
			case Setting::INPUT_PRICE_TYPE_WITHOUT_VAT:
				return 'Vstupní cena bez DPH';

			case Setting::INPUT_PRICE_TYPE_WITH_VAT:
				return 'Vstupní cena s DPH';

			default:
				throw new \SS6\ShopBundle\Model\Pricing\Exception\InvalidInputPriceTypeException(
					'Neplatný typ vstupní ceny: ' . $inputPriceType);
		}
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'input_price_label_extension';
	}
}
