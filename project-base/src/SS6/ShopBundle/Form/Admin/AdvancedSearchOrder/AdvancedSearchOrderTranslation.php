<?php

namespace SS6\ShopBundle\Form\Admin\AdvancedSearchOrder;

use SS6\ShopBundle\Component\Translation\Translator;

class AdvancedSearchOrderTranslation {

	/**
	 * @var \Symfony\Component\Translation\TranslatorInterface
	 */
	private $translator;

	/**
	 * @var string[filterName]
	 */
	private $filtersTranslations;

	public function __construct(Translator $translator) {
		$this->translator = $translator;

		$this->filtersTranslations = [
			'orderNumber' => $this->translator->trans('Číslo objednávky'),
			'orderCreatedAt' => $this->translator->trans('Vytvořeno dne'),
			'orderTotalPriceWithVat' => $this->translator->trans('Cena s DPH'),
			'orderDomain' => $this->translator->trans('Doména'),
			'orderStatus' => $this->translator->trans('Stav objednávky'),
			'orderProduct' => $this->translator->trans('Zboží v objednávce'),
		];
	}

	/**
	 * @param string $filterName
	 * @return string
	 */
	public function translateFilterName($filterName) {
		if (array_key_exists($filterName, $this->filtersTranslations)) {
			return $this->filtersTranslations[$filterName];
		}

		$message = 'Filter "' . $filterName . '" translation not found.';
		throw new \SS6\ShopBundle\Model\AdvancedSearchOrder\Exception\AdvancedSearchOrderTranslationNotFoundException($message);
	}
}
