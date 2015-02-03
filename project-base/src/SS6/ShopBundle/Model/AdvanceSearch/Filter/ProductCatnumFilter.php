<?php

namespace SS6\ShopBundle\Model\AdvanceSearch\Filter;

use Doctrine\ORM\QueryBuilder;
use SS6\ShopBundle\Component\String\DatabaseSearching;
use SS6\ShopBundle\Model\AdvanceSearch\AdvanceSearchFilterInterface;

class ProductCatnumFilter implements AdvanceSearchFilterInterface {

	/**
	 * {@inheritdoc}
	 */
	public function getName() {
		return 'productCatnum';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getAllowedOperators() {
		return [
			self::OPERATOR_CONTAINS,
			self::OPERATOR_NOT_CONTAINS,
			self::OPERATOR_NOT_SET,
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
		if ($operator === self::OPERATOR_NOT_SET) {
			$queryBuilder->andWhere('p.catnum IS NULL');
		} elseif ($operator === self::OPERATOR_CONTAINS || $operator === self::OPERATOR_NOT_CONTAINS) {
			if ($value === null) {
				$value = '';
			}

			$dqlOperator = $this->getContainsDqlOperator($operator);
			$searchValue = '%' . DatabaseSearching::getLikeSearchString($value) . '%';
			$queryBuilder->andWhere('NORMALIZE(p.catnum) ' . $dqlOperator . ' NORMALIZE(:productCatnum)');
			$queryBuilder->setParameter('productCatnum', $searchValue);
		}
	}

	/**
	 * @param string $operator
	 * @return string
	 */
	private function getContainsDqlOperator($operator) {
		switch ($operator) {
			case self::OPERATOR_CONTAINS:
				return 'LIKE';
			case self::OPERATOR_NOT_CONTAINS:
				return 'NOT LIKE';
		}
	}

}
