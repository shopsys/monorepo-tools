<?php

namespace Shopsys\FrameworkBundle\Model\AdvancedSearch;

use Doctrine\ORM\QueryBuilder;

class AdvancedSearchService
{
    const TEMPLATE_RULE_FORM_KEY = '__template__';

    /**
     * @var \Shopsys\FrameworkBundle\Model\AdvancedSearch\ProductAdvancedSearchConfig
     */
    private $advancedSearchConfig;

    /**
     * @param \Shopsys\FrameworkBundle\Model\AdvancedSearch\ProductAdvancedSearchConfig $advancedSearchConfig
     */
    public function __construct(ProductAdvancedSearchConfig $advancedSearchConfig)
    {
        $this->advancedSearchConfig = $advancedSearchConfig;
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param \Shopsys\FrameworkBundle\Model\AdvancedSearch\AdvancedSearchRuleData[] $advancedSearchData
     */
    public function extendQueryBuilderByAdvancedSearchData(QueryBuilder $queryBuilder, array $advancedSearchData)
    {
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
