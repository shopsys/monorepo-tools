<?php

namespace Shopsys\ShopBundle\Model\AdvancedSearch;

use Doctrine\ORM\QueryBuilder;

interface AdvancedSearchFilterInterface
{

    const OPERATOR_CONTAINS = 'contains';
    const OPERATOR_NOT_CONTAINS = 'notContains';
    const OPERATOR_NOT_SET = 'notSet';
    const OPERATOR_IS = 'is';
    const OPERATOR_IS_NOT = 'isNot';
    const OPERATOR_IS_USED = 'isUsed';
    const OPERATOR_IS_NOT_USED = 'isNotUsed';
    const OPERATOR_BEFORE = 'before';
    const OPERATOR_AFTER = 'after';
    const OPERATOR_GT = 'gt';
    const OPERATOR_GTE = 'gte';
    const OPERATOR_LT = 'lt';
    const OPERATOR_LTE = 'lte';

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string[]
     */
    public function getAllowedOperators();

    /**
     * @return string|\Symfony\Component\Form\FormTypeInterface
     */
    public function getValueFormType();

    /**
     * @return array
     */
    public function getValueFormOptions();

    /**
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param \Shopsys\ShopBundle\Model\AdvancedSearch\AdvancedSearchRuleData[] $rulesData
     */
    public function extendQueryBuilder(QueryBuilder $queryBuilder, $rulesData);
}
