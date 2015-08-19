<?php

namespace SS6\ShopBundle\Model\Product\Listing;

use SS6\ShopBundle\Component\Translation\Translator;
use SS6\ShopBundle\Model\Product\Listing\ProductListOrderingModeService;
use Symfony\Component\HttpFoundation\Request;

class ProductListOrderingModeForSearchFacade {

	const COOKIE_NAME = 'productSearchOrderingMode';

	/**
	 * @var \SS6\ShopBundle\Model\Product\Listing\ProductListOrderingModeService
	 */
	private $productListOrderingModeService;

	/**
	 * @var \SS6\ShopBundle\Component\Translation\Translator
	 */
	private $translator;

	public function __construct(
		ProductListOrderingModeService $productListOrderingModeService,
		Translator $translator
	) {
		$this->productListOrderingModeService = $productListOrderingModeService;
		$this->translator = $translator;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\Listing\ProductListOrderingConfig
	 */
	public function getProductListOrderingConfig() {
		return new ProductListOrderingConfig(
			[
				ProductListOrderingModeService::ORDER_BY_RELEVANCE => $this->translator->trans('relevance'),
				ProductListOrderingModeService::ORDER_BY_NAME_ASC => $this->translator->trans('abecedně A -> Z'),
				ProductListOrderingModeService::ORDER_BY_NAME_DESC => $this->translator->trans('abecedně Z -> A'),
				ProductListOrderingModeService::ORDER_BY_PRICE_ASC => $this->translator->trans('od nejlevnějšího'),
				ProductListOrderingModeService::ORDER_BY_PRICE_DESC => $this->translator->trans('od nejdražšího'),
			],
			ProductListOrderingModeService::ORDER_BY_RELEVANCE,
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
