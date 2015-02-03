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
	 * @param array|null $rulesData
	 * @return array
	 */
	public function getRulesFormDataByRequestData(array $rulesData = null) {
		if ($rulesData === null) {
			$searchRulesData = [];
		} else {
			$searchRulesData = array_values($rulesData);
		}

		if (count($searchRulesData) === 0) {
			$searchRulesData[] = $this->createDefaultRuleFormData('productName');
		}

		$searchRulesData[self::TEMPLATE_RULE_FORM_KEY] = $this->createDefaultRuleFormData('productName');

		return $searchRulesData;
	}

	/**
	 * @param string $filterName
	 * @return array
	 */
	public function createDefaultRuleFormData($filterName) {
		return [
			'subject' => $filterName,
			'operator' => null,
			'value' => null,
		];
	}

	/**
	 * @param \Doctrine\ORM\QueryBuilder $queryBuilder
	 * @param array $advancedSearchData
	 */
	public function extendQueryBuilderByAdvancedSearchData(QueryBuilder $queryBuilder, array $advancedSearchData) {
		foreach ($advancedSearchData as $key => $ruleData) {
			if ($key === self::TEMPLATE_RULE_FORM_KEY || $ruleData['operator'] === null) {
				continue;
			}
			$filter = $this->advancedSearchConfig->getFilter($ruleData['subject']);
			$filter->extendQueryBuilder($queryBuilder, $ruleData['operator'], $ruleData['value']);
		}
	}

}
