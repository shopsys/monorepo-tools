<?php

namespace Shopsys\ShopBundle\Model\AdvancedSearchOrder\Filter;

use Doctrine\ORM\QueryBuilder;
use Shopsys\ShopBundle\Form\FormType;
use Shopsys\ShopBundle\Model\AdvancedSearch\AdvancedSearchFilterInterface;

class OrderCreateDateFilter implements AdvancedSearchFilterInterface
{

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
            self::OPERATOR_IS,
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
            if ($ruleData->value === null) {
                continue;
            }

            $dateMidnight = clone $ruleData->value;
            /* @var $dateMidnight \DateTime */
            $dateMidnight->modify('midnight');

            $parameterName = 'orderCreatedAt_' . $index;
            $parameterName2 = 'orderCreatedAt_' . $index . '_2';

            if ($ruleData->operator === self::OPERATOR_BEFORE) {
                $queryBuilder->andWhere('o.createdAt < :' . $parameterName)
                    ->setParameter($parameterName, $dateMidnight);
            } elseif ($ruleData->operator === self::OPERATOR_AFTER) {
                $queryBuilder->andWhere('o.createdAt >= :' . $parameterName)
                    ->setParameter($parameterName, $dateMidnight);
            } elseif ($ruleData->operator === self::OPERATOR_IS) {
                $dateTomorrow = clone $dateMidnight;
                $dateTomorrow->modify('tomorrow');

                $queryBuilder->andWhere('o.createdAt BETWEEN :' . $parameterName . ' AND :' . $parameterName2)
                    ->setParameter($parameterName, $dateMidnight)
                    ->setParameter($parameterName2, $dateTomorrow);
            }
        }
    }

}
