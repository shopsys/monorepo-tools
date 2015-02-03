<?php

namespace SS6\ShopBundle\Form\Admin\AdvanceSearch;

use SS6\ShopBundle\Model\AdvanceSearch\AdvanceSearchFilterInterface;
use Symfony\Component\Translation\TranslatorInterface;

class AdvanceSearchTranslation {

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

	public function __construct(TranslatorInterface $translator) {
		$this->translator = $translator;

		$this->operatorsTranslations = [
			AdvanceSearchFilterInterface::OPERATOR_CONTAINS => $this->translator->trans('obsahuje'),
			AdvanceSearchFilterInterface::OPERATOR_NOT_CONTAINS => $this->translator->trans('neobsahuje'),
			AdvanceSearchFilterInterface::OPERATOR_NOT_SET => $this->translator->trans('není zadáno'),
			AdvanceSearchFilterInterface::OPERATOR_IS => $this->translator->trans('je'),
			AdvanceSearchFilterInterface::OPERATOR_IS_NOT => $this->translator->trans('není'),
		];

		$this->filtersTranslations = [
			'productCatnum' => $this->translator->trans('Katalogové číslo'),
			'productName' => $this->translator->trans('Název produktu'),
			'productPartno' => $this->translator->trans('Partno'),
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
		throw new \SS6\ShopBundle\Model\AdvanceSearch\Exception\AdvanceSearchTranslationNotFoundException($message);
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
		throw new \SS6\ShopBundle\Model\AdvanceSearch\Exception\AdvanceSearchTranslationNotFoundException($message);
	}
}
