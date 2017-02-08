<?php

namespace Shopsys\ShopBundle\Model\Product\Listing;

use Shopsys\ShopBundle\Model\Product\Listing\ProductListOrderingConfig;
use Symfony\Component\HttpFoundation\Request;

class ProductListOrderingModeService {

	const ORDER_BY_RELEVANCE = 'relevance';
	const ORDER_BY_NAME_ASC = 'name_asc';
	const ORDER_BY_NAME_DESC = 'name_desc';
	const ORDER_BY_PRICE_ASC = 'price_asc';
	const ORDER_BY_PRICE_DESC = 'price_desc';
	const ORDER_BY_PRIORITY = 'priority';

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param \Shopsys\ShopBundle\Model\Product\Listing\ProductListOrderingConfig $productListOrderingConfig
	 * @return string
	 */
	public function getOrderingModeFromRequest(
		Request $request,
		ProductListOrderingConfig $productListOrderingConfig
	) {
		$orderingMode = $request->cookies->get($productListOrderingConfig->getCookieName());

		if (!in_array($orderingMode, $this->getSupportedOrderingModes($productListOrderingConfig))) {
			$orderingMode = $productListOrderingConfig->getDefaultOrderingMode();
		}

		return $orderingMode;
	}

	/**
	 * @return string[]
	 */
	private function getSupportedOrderingModes(ProductListOrderingConfig $productListOrderingConfig) {
		return array_keys($productListOrderingConfig->getSupportedOrderingModesNames());
	}

}
