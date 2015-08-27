<?php

namespace SS6\ShopBundle\Model\Product\Parameter;

class ParameterValueData {

	/**
	 * @var string|null
	 */
	public $text;

	/**
	 * @var string|null
	 */
	public $locale;

	/**
	 * @param string|null $text
	 * @param string|null $locale
	 */
	public function __construct($text = null, $locale = null) {
		$this->text = $text;
		$this->locale = $locale;
	}

}
