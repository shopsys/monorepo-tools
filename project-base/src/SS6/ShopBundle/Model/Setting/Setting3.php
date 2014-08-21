<?php

namespace SS6\ShopBundle\Model\Setting;

class Setting3 {

	const INPUT_PRICE_TYPE = 'inputPriceType';

	const INPUT_PRICE_TYPE_WITH_VAT = 1;
	const INPUT_PRICE_TYPE_WITHOUT_VAT = 2;

	private $data = array(
		self::INPUT_PRICE_TYPE => self::INPUT_PRICE_TYPE_WITHOUT_VAT,
	);

	public function get($key) {
		return $this->data[$key];
	}

}
