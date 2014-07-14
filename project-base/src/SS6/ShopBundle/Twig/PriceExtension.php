<?php

namespace SS6\ShopBundle\Twig;

use Twig_Extension;
use Twig_SimpleFilter;

class PriceExtension extends Twig_Extension {

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
			return 'Zdarma';
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
