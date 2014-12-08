<?php

namespace SS6\ShopBundle\Twig;

use Symfony\Component\Translation\TranslatorInterface;
use Twig_Extension;
use Twig_SimpleFilter;

class PriceExtension extends Twig_Extension {

	/**
	 * @var \Symfony\Component\Translation\TranslatorInterface
	 */
	private $translator;

	public function __construct(TranslatorInterface $translator) {
		$this->translator = $translator;
	}

	/**
	 * @return array
	 */
	public function getFilters() {
		return array(
			new Twig_SimpleFilter('price', array($this, 'priceFilter'), array('is_safe' => array('html'))),
			new Twig_SimpleFilter('priceText', array($this, 'priceTextFilter'), array('is_safe' => array('html'))),
		);
	}

	/**
	 * @param string $price
	 * @return string
	 */
	public function priceFilter($price) {
		$price = (float)$price;
		$price = number_format($price, 2, ',', ' ');
		$price = htmlspecialchars($price, ENT_QUOTES, 'UTF-8') . '&nbsp;Kč';
		return $price;
	}

	/**
	 * @param string $price
	 * @return string
	 */
	public function priceTextFilter($price) {
		if ($price == 0) {
			return $this->translator->trans('Zdarma');
		} else {
			return $this->priceFilter($price);
		}
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'price_extension';
	}
}
