<?php

namespace SS6\ShopBundle\Model\AdvancedSearchOrder;

use Doctrine\ORM\QueryBuilder;
use SS6\ShopBundle\Model\AdvancedSearchOrder\AdvancedSearchOrderConfig;

class AdvancedSearchOrderService {

	const TEMPLATE_RULE_FORM_KEY = '__template__';

	/**
	 * @var \SS6\ShopBundle\Model\AdvancedSearchOrder\AdvancedSearchOrderConfig
	 */
	private $AdvancedSearchOrderConfig;

	public function __construct(AdvancedSearchOrderConfig $advancedSearchOrderConfig) {
		$this->AdvancedSearchOrderConfig = $advancedSearchOrderConfig;
	}

	/**
	 * @param array|null $requestData
	 * @return array
	 */
	public function getRulesFormViewDataByRequestData(array $requestData = null) {
		if ($requestData === null) {
			$searchRulesViewData = [];
		} else {
			$searchRulesViewData = array_values($requestData);
		}

		if (count($searchRulesViewData) === 0) {
			$searchRulesViewData[] = $this->createDefaultRuleFormViewData('orderTotalPriceWithVat');
		}

		$searchRulesViewData[self::TEMPLATE_RULE_FORM_KEY] = $this->createDefaultRuleFormViewData('orderTotalPriceWithVat');

		return $searchRulesViewData;
	}

	/**
	 * @param string $filterName
	 * @return array
	 */
	public function createDefaultRuleFormViewData($filterName) {
		return [
			'subject' => $filterName,
			'operator' => null,
			'value' => null,
		];
	}

	/**
	 * @param \Doctrine\ORM\QueryBuilder $queryBuilder
	 * @param \SS6\ShopBundle\Model\AdvancedSearchOrder\RuleData $advancedSearchOrderData
	 */
	public function extendQueryBuilderByAdvancedSearchOrderData(QueryBuilder $queryBuilder, array $advancedSearchOrderData) {
		$rulesDataByFilterName = [];
		foreach ($advancedSearchOrderData as $key => $ruleData) {
			if ($key === self::TEMPLATE_RULE_FORM_KEY || $ruleData->operator === null) {
				continue;
			}
			$rulesDataByFilterName[$ruleData->subject][] = $ruleData;
		}

		foreach ($rulesDataByFilterName as $filterName => $rulesData) {
			$filter = $this->AdvancedSearchOrderConfig->getFilter($filterName);
			$filter->extendQueryBuilder($queryBuilder, $rulesData);
		}
	}

}
