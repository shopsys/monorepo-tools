<?php

namespace SS6\ShopBundle\Model\Product;

use Symfony\Component\HttpFoundation\Request;

class ProductListOrderingService {

	const COOKIE_NAME = 'productListOrdering';

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @return \SS6\ShopBundle\Model\Product\ProductListOrderingSetting
	 */
	public function getOrderingSettingFromRequest(Request $request) {
		$orderingMode = $request->cookies->get(self::COOKIE_NAME);

		if (!in_array($orderingMode, ProductListOrderingSetting::getOrderingModes())) {
			$orderingMode = $this->getDefaultOrderingMode();
		}

		return new ProductListOrderingSetting($orderingMode);
	}

	/**
	 * @return string
	 */
	private function getDefaultOrderingMode() {
		return ProductListOrderingSetting::ORDER_BY_NAME_ASC;
	}

}
