<?php

namespace SS6\ShopBundle\Model\Product\Listing;

use SS6\ShopBundle\Component\Translation\Translator;
use SS6\ShopBundle\Model\Product\Listing\ProductListOrderingModeFacade;

class ProductListOrderingConfig {

	const COOKIE_NAME = 'productListOrdering';

	/**
	 * @var \SS6\ShopBundle\Component\Translation\Translator
	 */
	private $translator;

	public function __construct(Translator $translator) {
		$this->translator = $translator;
	}

	/**
	 * @return string[orderingMode]
	 */
	public function getSupportedOrderingModesNames() {
		return [
			ProductListOrderingModeFacade::ORDER_BY_NAME_ASC => $this->translator->trans('abecedně A -> Z'),
			ProductListOrderingModeFacade::ORDER_BY_NAME_DESC => $this->translator->trans('abecedně Z -> A'),
			ProductListOrderingModeFacade::ORDER_BY_PRICE_ASC => $this->translator->trans('od nejlevnějšího'),
			ProductListOrderingModeFacade::ORDER_BY_PRICE_DESC => $this->translator->trans('od nejdražšího'),
		];
	}

	/**
	 * @return string
	 */
	public function getCookieName() {
		return self::COOKIE_NAME;
	}

	/**
	 * @return string
	 */
	public function getDefaultOrderingMode() {
		return ProductListOrderingModeFacade::ORDER_BY_NAME_ASC;
	}

}
