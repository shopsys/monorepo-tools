<?php

namespace SS6\ShopBundle\Form\Admin\AdvancedSearch;

use SS6\ShopBundle\Component\Translation\Translator;

class AdvancedSearchTranslation {

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
			'productCatnum' => $this->translator->trans('Katalogové číslo'),
			'productName' => $this->translator->trans('Název produktu'),
			'productPartno' => $this->translator->trans('Partno'),
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
		throw new \SS6\ShopBundle\Model\AdvancedSearch\Exception\AdvancedSearchTranslationNotFoundException($message);
	}
}
