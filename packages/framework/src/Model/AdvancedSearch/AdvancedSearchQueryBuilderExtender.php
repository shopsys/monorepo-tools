<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Model\AdvancedSearch;

use Doctrine\ORM\QueryBuilder;

class AdvancedSearchQueryBuilderExtender
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\AdvancedSearch\AdvancedSearchConfig
     */
    private $advancedSearchConfig;

    /**
     * @param \Shopsys\FrameworkBundle\Model\AdvancedSearch\AdvancedSearchConfig $advancedSearchConfig
     */
    public function __construct(AdvancedSearchConfig $advancedSearchConfig)
    {
        $this->advancedSearchConfig = $advancedSearchConfig;
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param \Shopsys\FrameworkBundle\Model\AdvancedSearch\AdvancedSearchRuleData[] $advancedSearchData
     */
    public function extendByAdvancedSearchData(QueryBuilder $queryBuilder, array $advancedSearchData)
    {
        $rulesDataByFilterName = [];
        foreach ($advancedSearchData as $key => $ruleData) {
            if ($key === RuleFormViewDataFactory::TEMPLATE_RULE_FORM_KEY || $ruleData->operator === null) {
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
