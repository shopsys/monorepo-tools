<?php

class SomeClass
{
    /**
     * Method that applies the filtering conditions specified by $rulesData to the provided query builder
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param \Shopsys\FrameworkBundle\Model\AdvancedSearch\AdvancedSearchRuleData[] $rulesData
     */
    public function extendQueryBuilder(QueryBuilder $queryBuilder, $rulesData)
    {
    }
}
