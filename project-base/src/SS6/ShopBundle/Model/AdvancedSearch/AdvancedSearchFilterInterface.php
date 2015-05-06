<?php

namespace SS6\ShopBundle\Model\AdvancedSearch;

use Doctrine\ORM\QueryBuilder;

interface AdvancedSearchFilterInterface {

	const OPERATOR_CONTAINS = 'contains';
	const OPERATOR_NOT_CONTAINS = 'notContains';
	const OPERATOR_NOT_SET = 'notSet';
	const OPERATOR_IS = 'is';
	const OPERATOR_IS_NOT = 'isNot';
	const OPERATOR_BEFORE = 'before';
	const OPERATOR_AFTER = 'after';
	const OPERATOR_AT = 'at';
	const OPERATOR_EQ = 'eq';
	const OPERATOR_NEQ = 'neq';
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
	 * @return string|FormTypeInterface
	 */
	public function getValueFormType();

	/**
	 * @return array
	 */
	public function getValueFormOptions();

	/**
	 * @param \Doctrine\ORM\QueryBuilder $queryBuilder
	 * @param \SS6\ShopBundle\Model\AdvancedSearch\RuleData[] $rulesData
	 */
	public function extendQueryBuilder(QueryBuilder $queryBuilder, $rulesData);

}