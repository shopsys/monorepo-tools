<?php

namespace Shopsys\ShopBundle\Model\AdvancedSearch\Filter;

use Doctrine\ORM\QueryBuilder;
use Shopsys\ShopBundle\Model\AdvancedSearch\AdvancedSearchFilterInterface;

class ProductCalculatedSellingDeniedFilter implements AdvancedSearchFilterInterface {

    /**
     * {@inheritdoc}
     */
    public function getName() {
        return 'productCalculatedSellingDenied';
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
        return 'hidden';
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
            $sellingDenied = $ruleData->operator === self::OPERATOR_IS;

            $parameterName = 'calculatedsellingDenied_' . $index;
            $queryBuilder->andWhere('p.calculatedSellingDenied = :' . $parameterName)
                ->setParameter($parameterName, $sellingDenied);
        }
    }

}
