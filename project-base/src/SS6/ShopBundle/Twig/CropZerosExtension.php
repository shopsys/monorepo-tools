<?php

namespace SS6\ShopBundle\Twig;

use Twig_Extension;
use Twig_SimpleFilter;

class CropZerosExtension extends Twig_Extension {

	/**
	 * @return array
	 */
	public function getFilters() {
		return array(
			new Twig_SimpleFilter('cropZeros', array($this, 'cropZeros')),
		);
	}

	/**
	 * @param string $value
	 * @return string
	 */
	public function cropZeros($value) {
		return preg_replace('/(?:[,.]0+|([,.]\d*?)0+)$/', '$1', $value);
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'cropZeros';
	}

}
