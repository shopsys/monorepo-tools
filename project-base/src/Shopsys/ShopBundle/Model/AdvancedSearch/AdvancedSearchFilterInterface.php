<?php

namespace Shopsys\FrameworkBundle\Model\AdvancedSearch;

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
     * Returns a unique name of the filter
     *
     * @return string
     */
    public function getName();

    /**
     * Returns an array of OPERATOR_* constants specifying which operators can be used with this filter
     *
     * @return string[]
     */
    public function getAllowedOperators();

    /**
     * Returns a form type that should be used for value selection
     *
     * @return string|\Symfony\Component\Form\FormTypeInterface
     */
    public function getValueFormType();

    /**
     * Returns options that will be passed to the form type used for value selection
     *
     * @return array
     */
    public function getValueFormOptions();

    /**
     * Method that applies the filtering conditions specified by $rulesData to the provided query builder
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param \Shopsys\FrameworkBundle\Model\AdvancedSearch\AdvancedSearchRuleData[] $rulesData
     */
    public function extendQueryBuilder(QueryBuilder $queryBuilder, $rulesData);
}
