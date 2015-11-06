<?php

namespace SS6\ShopBundle\Form\Admin\AdvancedSearch;

use SS6\ShopBundle\Model\AdvancedSearch\AdvancedSearchFilterInterface;

class AdvancedSearchOperatorTranslation {

	/**
	 * @var string[operator]
	 */
	private $operatorsTranslations;

	public function __construct() {
		$this->operatorsTranslations = [
			AdvancedSearchFilterInterface::OPERATOR_CONTAINS => t('obsahuje'),
			AdvancedSearchFilterInterface::OPERATOR_NOT_CONTAINS => t('neobsahuje'),
			AdvancedSearchFilterInterface::OPERATOR_NOT_SET => t('není zadáno'),
			AdvancedSearchFilterInterface::OPERATOR_IS => t('je'),
			AdvancedSearchFilterInterface::OPERATOR_IS_NOT => t('není'),
			AdvancedSearchFilterInterface::OPERATOR_IS_USED => t('používá'),
			AdvancedSearchFilterInterface::OPERATOR_IS_NOT_USED => t('nepoužívá'),
			AdvancedSearchFilterInterface::OPERATOR_BEFORE => t('před'),
			AdvancedSearchFilterInterface::OPERATOR_AFTER => t('po'),
			AdvancedSearchFilterInterface::OPERATOR_GT => t('větší než'),
			AdvancedSearchFilterInterface::OPERATOR_LT => t('menší než'),
			AdvancedSearchFilterInterface::OPERATOR_GTE => t('větší nebo rovno'),
			AdvancedSearchFilterInterface::OPERATOR_LTE => t('menší nebo rovno'),
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
		throw new \SS6\ShopBundle\Model\AdvancedSearch\Exception\AdvancedSearchTranslationNotFoundException($message);
	}

}
