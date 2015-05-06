<?php

namespace SS6\ShopBundle\Form\Admin\AdvancedSearchOrder;

use SS6\ShopBundle\Component\Translation\Translator;
use SS6\ShopBundle\Model\AdvancedSearchOrder\AdvancedSearchOrderFilterInterface;

class AdvancedSearchOrderTranslation {

	/**
	 * @var \Symfony\Component\Translation\TranslatorInterface
	 */
	private $translator;

	/**
	 * @var string[operator]
	 */
	private $operatorsTranslations;

	/**
	 * @var string[filterName]
	 */
	private $filtersTranslations;

	public function __construct(Translator $translator) {
		$this->translator = $translator;

		$this->operatorsTranslations = [
			AdvancedSearchOrderFilterInterface::OPERATOR_CONTAINS => $this->translator->trans('obsahuje'),
			AdvancedSearchOrderFilterInterface::OPERATOR_NOT_CONTAINS => $this->translator->trans('neobsahuje'),
			AdvancedSearchOrderFilterInterface::OPERATOR_NOT_SET => $this->translator->trans('není zadáno'),
			AdvancedSearchOrderFilterInterface::OPERATOR_IS => $this->translator->trans('je'),
			AdvancedSearchOrderFilterInterface::OPERATOR_IS_NOT => $this->translator->trans('není'),
			AdvancedSearchOrderFilterInterface::OPERATOR_AT => $this->translator->trans('je'),
			AdvancedSearchOrderFilterInterface::OPERATOR_BEFORE => $this->translator->trans('před'),
			AdvancedSearchOrderFilterInterface::OPERATOR_AFTER => $this->translator->trans('po'),
			AdvancedSearchOrderFilterInterface::OPERATOR_EQ => $this->translator->trans('je'),
			AdvancedSearchOrderFilterInterface::OPERATOR_NEQ => $this->translator->trans('není'),
			AdvancedSearchOrderFilterInterface::OPERATOR_GT => $this->translator->trans('větší než'),
			AdvancedSearchOrderFilterInterface::OPERATOR_LT => $this->translator->trans('menší než'),
			AdvancedSearchOrderFilterInterface::OPERATOR_GTE => $this->translator->trans('větší nebo rovno'),
			AdvancedSearchOrderFilterInterface::OPERATOR_LTE => $this->translator->trans('menší nebo rovno'),
		];

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
	 * @param string $operator
	 * @return string
	 */
	public function translateOperator($operator) {
		if (array_key_exists($operator, $this->operatorsTranslations)) {
			return $this->operatorsTranslations[$operator];
		}

		$message = 'Operator "' . $operator . '" translation not found.';
		throw new \SS6\ShopBundle\Model\AdvancedSearchOrder\Exception\AdvancedSearchOrderTranslationNotFoundException($message);
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
