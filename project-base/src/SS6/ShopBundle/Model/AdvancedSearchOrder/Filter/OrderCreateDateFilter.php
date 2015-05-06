<?php
/**
 * Author: Jakub Dolba
 * Date: 25. 4. 2015
 * Description:
 */

namespace SS6\ShopBundle\Model\AdvancedSearchOrder\Filter;

use Doctrine\ORM\QueryBuilder;
use SS6\ShopBundle\Form\FormType;
use SS6\ShopBundle\Model\AdvancedSearchOrder\AdvancedSearchOrderFilterInterface;
use Symfony\Component\Validator\Constraints\DateTime;

class OrderCreateDateFilter implements AdvancedSearchOrderFilterInterface {

	/**
	 * {@inheritdoc}
	 */
	public function getName() {
		return 'orderCreatedAt';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getAllowedOperators() {
		return [
			self::OPERATOR_AFTER,
			self::OPERATOR_BEFORE,
			self::OPERATOR_AT,
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function getValueFormType() {
		return FormType::DATE_PICKER;
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
			if ($ruleData->operator === self::OPERATOR_AFTER || $ruleData->operator === self::OPERATOR_BEFORE ||
				$ruleData->operator === self::OPERATOR_AT) {
				if ($ruleData->value === null || empty($ruleData->value)) {
					$searchValue = new \DateTime();
				} else {
					$searchValue = $ruleData->value;
				}

				$dqlOperator = $this->getContainsDqlOperator($ruleData->operator);
				$parameterName = 'orderCreatedAt_' . $index;
				$parameterName2 = 'orderCreatedAt_' . $index . '_2';

				$where = 'o.createdAt ' . $dqlOperator . ' :' . $parameterName;

				if ($ruleData->operator === self::OPERATOR_AT) {
					/** @var $searchValue \DateTime */
					$searchValue2 = clone $searchValue;
					$searchValue2 = $searchValue2->modify('+1 day')->format('Y-m-d');
					$searchValue = $searchValue->format('Y-m-d');
					$where = 'o.createdAt ' . $dqlOperator . ' :' . $parameterName . ' AND :' . $parameterName2;
				}

				$queryBuilder->andWhere($where);
				$queryBuilder->setParameter($parameterName, $searchValue);
				if ($ruleData->operator === self::OPERATOR_AT) {
					$queryBuilder->setParameter($parameterName2, $searchValue2);
				}
			}
		}
	}

	/**
	 * @param string $operator
	 * @return string
	 */
	private function getContainsDqlOperator($operator) {
		switch ($operator) {
			case self::OPERATOR_AFTER:
				return '>=';
			case self::OPERATOR_BEFORE:
				return '<';
			case self::OPERATOR_AT:
			return 'BETWEEN';
		}
	}
}