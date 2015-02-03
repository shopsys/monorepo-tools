<?php

namespace SS6\ShopBundle\Model\AdvanceSearch;

use Doctrine\ORM\QueryBuilder;
use SS6\ShopBundle\Model\AdvanceSearch\AdvanceSearchConfig;

class AdvanceSearchService {

	const TEMPLATE_RULE_FORM_KEY = '__template__';

	/**
	 * @var \SS6\ShopBundle\Model\AdvanceSearch\AdvanceSearchConfig
	 */
	private $advanceSearchConfig;

	public function __construct(AdvanceSearchConfig $advanceSearchConfig) {
		$this->advanceSearchConfig = $advanceSearchConfig;
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
	 * @param array $advanceSearchData
	 */
	public function extendQueryBuilderByAdvanceSearchData(QueryBuilder $queryBuilder, array $advanceSearchData) {
		foreach ($advanceSearchData as $key => $ruleData) {
			if ($key === self::TEMPLATE_RULE_FORM_KEY || $ruleData['operator'] === null) {
				continue;
			}
			$filter = $this->advanceSearchConfig->getFilter($ruleData['subject']);
			$filter->extendQueryBuilder($queryBuilder, $ruleData['operator'], $ruleData['value']);
		}
	}

}
