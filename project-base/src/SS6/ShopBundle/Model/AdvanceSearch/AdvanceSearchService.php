<?php

namespace SS6\ShopBundle\Model\AdvanceSearch;

class AdvanceSearchService {

	const TEMPLATE_RULE_FORM_KEY = '__template__';

	/**
	 * @param array|null $rulesData
	 * @return array
	 */
	public function getRulesFormDataByRequestData(array $rulesData = null) {
		$rulesData = (array)$rulesData;

		$searchRulesData = [];
		foreach ($rulesData as $ruleData) {
			$searchRulesData[] = $ruleData;
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

}
