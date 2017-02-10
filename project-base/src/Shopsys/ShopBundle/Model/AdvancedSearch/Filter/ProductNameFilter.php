<?php

namespace Shopsys\ShopBundle\Model\AdvancedSearch\Filter;

use Doctrine\ORM\QueryBuilder;
use Shopsys\ShopBundle\Component\String\DatabaseSearching;
use Shopsys\ShopBundle\Model\AdvancedSearch\AdvancedSearchFilterInterface;

class ProductNameFilter implements AdvancedSearchFilterInterface
{

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
            self::OPERATOR_CONTAINS,
            self::OPERATOR_NOT_CONTAINS,
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
    public function extendQueryBuilder(QueryBuilder $queryBuilder, $rulesData) {
        foreach ($rulesData as $index => $ruleData) {
            if ($ruleData->value === null) {
                $searchValue = '%';
            } else {
                $searchValue = '%' . DatabaseSearching::getLikeSearchString($ruleData->value) . '%';
            }
            $dqlOperator = $this->getDqlOperator($ruleData->operator);
            $parameterName = 'productName_' . $index;
            $queryBuilder->andWhere('NORMALIZE(pt.name) ' . $dqlOperator . ' NORMALIZE(:' . $parameterName . ')');
            $queryBuilder->setParameter($parameterName, $searchValue);
        }
    }

    /**
     * @param string $operator
     * @return string
     */
    private function getDqlOperator($operator) {
        switch ($operator) {
            case self::OPERATOR_CONTAINS:
                return 'LIKE';
            case self::OPERATOR_NOT_CONTAINS:
                return 'NOT LIKE';
        }
    }

}
