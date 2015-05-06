<?php

namespace SS6\ShopBundle\Model\AdvancedSearchOrder\Filter;

use Doctrine\ORM\QueryBuilder;
use SS6\ShopBundle\Form\FormType;
use SS6\ShopBundle\Model\AdvancedSearchOrder\AdvancedSearchOrderFilterInterface;
use SS6\ShopBundle\Model\Order\Item\OrderProduct;
use SS6\ShopBundle\Model\Product\Product;

class OrderProductFilter implements AdvancedSearchOrderFilterInterface {

	/**
	 * {@inheritdoc}
	 */
	public function getName() {
		return 'orderProduct';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getAllowedOperators() {
		return [
			self::OPERATOR_CONTAINS,
			self::OPERATOR_NOT_CONTAINS,
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function getValueFormType() {
		return FormType::PRODUCT;
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
	public function extendQueryBuilder(QueryBuilder $queryBuilder, $rulesData) {
		foreach ($rulesData as $index => $ruleData) {
			if ($ruleData->operator === self::OPERATOR_CONTAINS || $ruleData->operator === self::OPERATOR_NOT_CONTAINS) {
				$searchValue = $ruleData->value;
				/** @var $searchValue Product */
				if($searchValue === null){
					continue;
				}
				$dqlOperator = $this->getContainsDqlOperator($ruleData->operator);
				$parameterName = 'orderProduct_' . $index;
				$tableAlias = 'oi_' . $index;
				$queryBuilder->andWhere($dqlOperator . ' (SELECT 1 FROM ' . OrderProduct::class . ' ' . $tableAlias . ' ' .
				'WHERE ' . $tableAlias . '.order = o AND ' . $tableAlias . '.product = :' . $parameterName . ')');
				$queryBuilder->setParameter($parameterName, $searchValue);
			}
		}
	}

	/**
	 * @param string $operator
	 * @return string
	 */
	private function getContainsDqlOperator($operator) {
		switch ($operator) {
			case self::OPERATOR_CONTAINS:
				return 'EXISTS';
			case self::OPERATOR_NOT_CONTAINS:
				return 'NOT EXISTS';
		}
	}
}