<?php

namespace SS6\ShopBundle\Model\Product\Listing;

use SS6\ShopBundle\Model\Product\Listing\ProductListOrderingModeService;
use Symfony\Component\HttpFoundation\Request;

class ProductListOrderingModeForBrandFacade {

	const COOKIE_NAME = 'productListOrderingModeForBrand';

	/**
	 * @var \SS6\ShopBundle\Model\Product\Listing\ProductListOrderingModeService
	 */
	private $productListOrderingModeService;

	public function __construct(ProductListOrderingModeService $productListOrderingModeService) {
		$this->productListOrderingModeService = $productListOrderingModeService;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\Listing\ProductListOrderingConfig
	 */
	public function getProductListOrderingConfig() {
		return new ProductListOrderingConfig(
			[
				ProductListOrderingModeService::ORDER_BY_PRIORITY => t('TOP'),
				ProductListOrderingModeService::ORDER_BY_NAME_ASC => t('abecedně A -> Z'),
				ProductListOrderingModeService::ORDER_BY_NAME_DESC => t('abecedně Z -> A'),
				ProductListOrderingModeService::ORDER_BY_PRICE_ASC => t('od nejlevnějšího'),
				ProductListOrderingModeService::ORDER_BY_PRICE_DESC => t('od nejdražšího'),
			],
			ProductListOrderingModeService::ORDER_BY_PRIORITY,
			self::COOKIE_NAME
		);
	}

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @return string
	 */
	public function getOrderingModeFromRequest(Request $request) {
		return $this->productListOrderingModeService->getOrderingModeFromRequest(
			$request,
			$this->getProductListOrderingConfig()
		);
	}

}
