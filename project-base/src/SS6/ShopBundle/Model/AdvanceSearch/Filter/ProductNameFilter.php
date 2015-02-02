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
		$searchValue = '%' . DatabaseSearching::getLikeSearchString($value) . '%';
		$queryBuilder->andWhere('NORMALIZE(pt.name) ' . $dqlOperator . ' NORMALIZE(:productName)');
		$queryBuilder->setParameter('productName', $searchValue);
	}

	/**
	 * @param string $operator
	 * @return string
	 */
	private function getDqlOperator($operator) {
		switch ($operator) {
			case self::OPERATOR_CONTAIN:
				return 'LIKE';
			case self::OPERATOR_NOT_CONTAIN:
				return 'NOT LIKE';
		}
	}

}
