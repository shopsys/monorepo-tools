<?php

namespace SS6\ShopBundle\Twig;

use SS6\ShopBundle\Model\Pricing\PricingSetting;
use Symfony\Component\Translation\TranslatorInterface;
use Twig_Extension;
use Twig_SimpleFunction;

class InputPriceLabelExtension extends Twig_Extension {

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\PricingSetting
	 */
	private $pricingSetting;

	/**
	 * @var \Symfony\Component\Translation\TranslatorInterface
	 */
	private $translator;

	public function __construct(PricingSetting $pricingSetting, TranslatorInterface $translator) {
		$this->pricingSetting = $pricingSetting;
		$this->translator = $translator;
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
	 */
	public function getInputPriceLabel() {
		$inputPriceType = $this->pricingSetting->getInputPriceType();

		switch ($inputPriceType) {
			case PricingSetting::INPUT_PRICE_TYPE_WITHOUT_VAT:
				return $this->translator->trans('Vstupní cena bez DPH');

			case PricingSetting::INPUT_PRICE_TYPE_WITH_VAT:
				return $this->translator->trans('Vstupní cena s DPH');

			default:
				throw new \SS6\ShopBundle\Model\Pricing\Exception\InvalidInputPriceTypeException(
					'Invalid input price type: ' . $inputPriceType);
		}
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'input_price_label_extension';
	}
}
