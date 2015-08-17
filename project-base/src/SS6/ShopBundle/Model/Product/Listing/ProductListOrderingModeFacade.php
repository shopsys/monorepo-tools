<?php

namespace SS6\ShopBundle\Model\Product\Listing;

use SS6\ShopBundle\Component\Translation\Translator;
use Symfony\Component\HttpFoundation\Request;

class ProductListOrderingModeFacade {

	const COOKIE_NAME = 'productListOrdering';

	const ORDER_BY_NAME_ASC = 'name_asc';
	const ORDER_BY_NAME_DESC = 'name_desc';
	const ORDER_BY_PRICE_ASC = 'price_asc';
	const ORDER_BY_PRICE_DESC = 'price_desc';

	/**
	 * @var \SS6\ShopBundle\Component\Translation\Translator
	 */
	private $translator;

	public function __construct(Translator $translator) {
		$this->translator = $translator;
	}

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @return string
	 */
	public function getOrderingModeFromRequest(Request $request) {
		$orderingMode = $request->cookies->get(self::COOKIE_NAME);

		if (!in_array($orderingMode, $this->getSupportedOrderingModes())) {
			$orderingMode = $this->getDefaultOrderingMode();
		}

		return $orderingMode;
	}

	/**
	 * @return string[orderingMode]
	 */
	public function getSupportedOrderingModesNames() {
		return [
			self::ORDER_BY_NAME_ASC => $this->translator->trans('abecedně A -> Z'),
			self::ORDER_BY_NAME_DESC => $this->translator->trans('abecedně Z -> A'),
			self::ORDER_BY_PRICE_ASC => $this->translator->trans('od nejlevnějšího'),
			self::ORDER_BY_PRICE_DESC => $this->translator->trans('od nejdražšího'),
		];
	}

	/**
	 * @return string[]
	 */
	private function getSupportedOrderingModes() {
		return array_keys($this->getSupportedOrderingModesNames());
	}

	/**
	 * @return string
	 */
	private function getDefaultOrderingMode() {
		return self::ORDER_BY_NAME_ASC;
	}

	/**
	 * @return string
	 */
	public function getCookieName() {
		return self::COOKIE_NAME;
	}

}
