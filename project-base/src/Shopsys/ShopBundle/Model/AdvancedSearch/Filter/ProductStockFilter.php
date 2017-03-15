<?php

namespace Shopsys\ShopBundle\Model\AdvancedSearch\Filter;

use Doctrine\ORM\QueryBuilder;
use Shopsys\ShopBundle\Model\AdvancedSearch\AdvancedSearchFilterInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class ProductStockFilter implements AdvancedSearchFilterInterface
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'productStock';
    }

    /**
     * {@inheritdoc}
     */
    public function getAllowedOperators()
    {
        return [
            self::OPERATOR_IS_USED,
            self::OPERATOR_IS_NOT_USED,
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
            $usingStock = $ruleData->operator === self::OPERATOR_IS_USED;

            $parameterName = 'usingStock_' . $index;
            $queryBuilder->andWhere('p.usingStock = :' . $parameterName)
                ->setParameter($parameterName, $usingStock);
        }
    }
}
