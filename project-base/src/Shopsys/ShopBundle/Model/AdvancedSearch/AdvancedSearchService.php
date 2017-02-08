<?php

namespace Shopsys\ShopBundle\Model\AdvancedSearch;

use Doctrine\ORM\QueryBuilder;
use Shopsys\ShopBundle\Model\AdvancedSearch\ProductAdvancedSearchConfig;

class AdvancedSearchService {

	const TEMPLATE_RULE_FORM_KEY = '__template__';

	/**
	 * @var \Shopsys\ShopBundle\Model\AdvancedSearch\ProductAdvancedSearchConfig
	 */
	private $advancedSearchConfig;

	public function __construct(ProductAdvancedSearchConfig $advancedSearchConfig) {
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
	 * @param \Shopsys\ShopBundle\Model\AdvancedSearch\AdvancedSearchRuleData[] $advancedSearchData
	 */
	public function extendQueryBuilderByAdvancedSearchData(QueryBuilder $queryBuilder, array $advancedSearchData) {
		$rulesDataByFilterName = [];
		foreach ($advancedSearchData as $key => $ruleData) {
			if ($key === self::TEMPLATE_RULE_FORM_KEY || $ruleData->operator === null) {
				continue;
			}
			$rulesDataByFilterName[$ruleData->subject][] = $ruleData;
		}

		foreach ($rulesDataByFilterName as $filterName => $rulesData) {
			$filter = $this->advancedSearchConfig->getFilter($filterName);
			$filter->extendQueryBuilder($queryBuilder, $rulesData);
		}
	}

}
