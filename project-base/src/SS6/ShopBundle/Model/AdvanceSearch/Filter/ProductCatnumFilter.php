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
		$queryBuilder->andWhere('NORMALIZE(p.catnum) ' . $dqlOperator . ' NORMALIZE(:productCatnum)');
		$queryBuilder->setParameter('productCatnum', DatabaseSearching::getLikeSearchString($value));
	}

	/**
	 * @param string $operator
	 * @return string
	 */
	private function getDqlOperator($operator) {
		switch ($operator) {
			case self::OPERATOR_IS:
				return 'LIKE';
			case self::OPERATOR_IS_NOT:
				return 'NOT LIKE';
		}
	}

}
