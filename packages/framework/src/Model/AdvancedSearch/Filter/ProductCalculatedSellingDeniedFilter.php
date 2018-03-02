<?php

namespace Shopsys\FrameworkBundle\Model\AdvancedSearch\Filter;

use Doctrine\ORM\QueryBuilder;
use Shopsys\FrameworkBundle\Model\AdvancedSearch\AdvancedSearchFilterInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class ProductCalculatedSellingDeniedFilter implements AdvancedSearchFilterInterface
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'productCalculatedSellingDenied';
    }

    /**
     * {@inheritdoc}
     */
    public function getAllowedOperators()
    {
        return [
            self::OPERATOR_IS,
            self::OPERATOR_IS_NOT,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getValueFormType()
    {
        return HiddenType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getValueFormOptions()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function extendQueryBuilder(QueryBuilder $queryBuilder, $rulesData)
    {
        foreach ($rulesData as $index => $ruleData) {
            $sellingDenied = $ruleData->operator === self::OPERATOR_IS;

            $parameterName = 'calculatedsellingDenied_' . $index;
            $queryBuilder->andWhere('p.calculatedSellingDenied = :' . $parameterName)
                ->setParameter($parameterName, $sellingDenied);
        }
    }
}
