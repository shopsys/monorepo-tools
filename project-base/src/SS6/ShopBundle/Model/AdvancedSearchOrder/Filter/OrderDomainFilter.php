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

class OrderDomainFilter implements AdvancedSearchOrderFilterInterface {
	/**
	 * {@inheritdoc}
	 */
	public function getName() {
		return 'orderDomain';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getAllowedOperators() {
		return [AdvancedSearchOrderFilterInterface::OPERATOR_IS,
			AdvancedSearchOrderFilterInterface::OPERATOR_IS_NOT,
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function getValueFormType() {
		return FormType::DOMAIN;
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
			if ($ruleData->operator === self::OPERATOR_IS || $ruleData->operator === self::OPERATOR_IS_NOT) {
				$dqlOperator = $this->getContainsDqlOperator($ruleData->operator);
				$parameterName = 'orderDomain_' . $index;
				$queryBuilder->andWhere('o.domainId ' . $dqlOperator . ' :' . $parameterName);
				$queryBuilder->setParameter($parameterName, $ruleData->value);
			}
		}
	}

	/**
	 * @param string $operator
	 * @return string
	 */
	private function getContainsDqlOperator($operator) {
		switch ($operator) {
			case self::OPERATOR_IS:
				return '=';
			case self::OPERATOR_IS_NOT:
				return '!=';
		}
	}
}