<?php

namespace SS6\ShopBundle\Model\AdvanceSearch\Filter;

use Doctrine\ORM\QueryBuilder;
use SS6\ShopBundle\Component\String\DatabaseSearching;
use SS6\ShopBundle\Model\AdvanceSearch\AdvanceSearchFilterInterface;

class ProductNameFilter implements AdvanceSearchFilterInterface {

	/**
	 * {@inheritdoc}
	 */
	public function getName() {
		return 'productName';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getAllowedOperators() {
		return [
			self::OPERATOR_CONTAIN,
			self::OPERATOR_NOT_CONTAIN,
			self::OPERATOR_IS,
			self::OPERATOR_IS_NOT,
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function getValueFormType() {
		return 'text';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getValueFormOptions() {
		return [];
	}

	/**
	 * {@inheritdoc}
	 */
	public function extendQueryBuilder(QueryBuilder $queryBuilder, $operator, $value) {
		if ($value === null) {
			$value = '';
		}
		$dqlOperator = $this->getDqlOperator($operator);
		$queryBuilder->andWhere('NORMALIZE(pt.name) ' . $dqlOperator . ' NORMALIZE(:productName)');
		$queryBuilder->setParameter('productName', $this->prepareValueByOperator($operator, $value));
	}

	/**
	 * @param string $operator
	 * @return string
	 */
	private function getDqlOperator($operator) {
		switch ($operator) {
			case self::OPERATOR_CONTAIN:
			case self::OPERATOR_IS:
				return 'LIKE';
			case self::OPERATOR_NOT_CONTAIN:
			case self::OPERATOR_IS_NOT:
				return 'NOT LIKE';
		}
	}

	/**
	 * @param string $operator
	 * @param string $value
	 * @return string
	 */
	private function prepareValueByOperator($operator, $value) {
		$value = DatabaseSearching::getLikeSearchString($value);
		switch ($operator) {
			case self::OPERATOR_CONTAIN:
			case self::OPERATOR_NOT_CONTAIN:
				return '%' . $value . '%';
			case self::OPERATOR_IS:
			case self::OPERATOR_IS_NOT:
				return $value;
		}
	}

}
