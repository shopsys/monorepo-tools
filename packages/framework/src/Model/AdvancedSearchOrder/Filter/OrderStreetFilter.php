<?php

namespace Shopsys\FrameworkBundle\Model\AdvancedSearchOrder\Filter;

use Doctrine\ORM\QueryBuilder;
use Shopsys\FrameworkBundle\Component\String\DatabaseSearching;
use Shopsys\FrameworkBundle\Model\AdvancedSearch\AdvancedSearchFilterInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class OrderStreetFilter implements AdvancedSearchFilterInterface
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'customerStreet';
    }

    /**
     * {@inheritdoc}
     */
    public function getAllowedOperators()
    {
        return [
            self::OPERATOR_CONTAINS,
            self::OPERATOR_NOT_CONTAINS,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getValueFormType()
    {
        return TextType::class;
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
            if ($ruleData->value === null || $ruleData->value == '') {
                $searchValue = '%';
            } else {
                $searchValue = DatabaseSearching::getFullTextLikeSearchString($ruleData->value);
            }
            $dqlOperator = $this->getContainsDqlOperator($ruleData->operator);
            $parameterName = 'city_' . $index;
            $queryBuilder->andWhere('NORMALIZE(o.street) ' . $dqlOperator . ' NORMALIZE(:' . $parameterName . ') OR NORMALIZE(o.deliveryStreet) ' . $dqlOperator . ' NORMALIZE(:' . $parameterName . ')');
            $queryBuilder->setParameter($parameterName, $searchValue);
        }
    }

    /**
     * @param string $operator
     * @return string
     */
    private function getContainsDqlOperator($operator)
    {
        switch ($operator) {
            case self::OPERATOR_CONTAINS:
                return 'LIKE';
            case self::OPERATOR_NOT_CONTAINS:
                return 'NOT LIKE';
        }
    }
}
