<?php

namespace SS6\ShopBundle\Model\AdvancedSearch;

use Doctrine\ORM\QueryBuilder;
use SS6\ShopBundle\Model\AdvancedSearch\AdvancedSearchConfig;

class AdvancedSearchService {

	const TEMPLATE_RULE_FORM_KEY = '__template__';

	/**
	 * @var \SS6\ShopBundle\Model\AdvancedSearch\AdvancedSearchConfig
	 */
	private $advancedSearchConfig;

	public function __construct(AdvancedSearchConfig $advancedSearchConfig) {
		$this->advancedSearchConfig = $advancedSearchConfig;
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
			$searchRulesViewData[] = $this->createDefaultRuleFormViewData('productName');
		}

		$searchRulesViewData[self::TEMPLATE_RULE_FORM_KEY] = $this->createDefaultRuleFormViewData('productName');

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
	 * @param \SS6\ShopBundle\Model\AdvancedSearch\RuleData $advancedSearchData
	 */
	public function extendQueryBuilderByAdvancedSearchData(QueryBuilder $queryBuilder, array $advancedSearchData) {
		foreach ($advancedSearchData as $key => $ruleData) {
			if ($key === self::TEMPLATE_RULE_FORM_KEY || $ruleData->operator === null) {
				continue;
			}
			$filter = $this->advancedSearchConfig->getFilter($ruleData->subject);
			$filter->extendQueryBuilder($queryBuilder, $ruleData->operator, $ruleData->value);
		}
	}

}
