<?php

namespace SS6\ShopBundle\Model\Product;

class ProductListOrderingSetting {

	const ORDER_BY_NAME_ASC = 'name_asc';
	const ORDER_BY_NAME_DESC = 'name_desc';

	private $orderingMode;

	public function __construct($orderingMode) {
		$this->setOrderingMode($orderingMode);
	}

	private function setOrderingMode($orderingMode) {
		if (!in_array($orderingMode, self::getOrderingModes())) {
			throw new Exception('TODO: InvalidOrderingMode');
		}

		$this->orderingMode = $orderingMode;
	}

	public function getOrderingMode() {
		return $this->orderingMode;
	}

	public static function getOrderingModes() {
		return array(
			self::ORDER_BY_NAME_ASC,
			self::ORDER_BY_NAME_DESC,
		);
	}

}
