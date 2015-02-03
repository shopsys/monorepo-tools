<?php

namespace SS6\ShopBundle\Model\AdvanceSearch;

use Doctrine\ORM\QueryBuilder;

interface AdvanceSearchFilterInterface {

	const OPERATOR_CONTAINS = 'contains';
	const OPERATOR_NOT_CONTAINS = 'notContains';
	const OPERATOR_NOT_SET = 'notSet';
	const OPERATOR_IS = 'is';
	const OPERATOR_IS_NOT = 'isNot';

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
	 * @param string $operator
	 * @param mixed $value
	 */
	public function extendQueryBuilder(QueryBuilder $queryBuilder, $operator, $value);

}