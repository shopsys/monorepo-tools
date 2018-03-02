<?php

namespace Shopsys\FrameworkBundle\Model\Product\Filter;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Shopsys\FrameworkBundle\Model\Product\Parameter\ProductParameterValue;

class ParameterFilterRepository
{
    /**
     * @param \Doctrine\ORM\QueryBuilder $productsQueryBuilder
     * @param \Shopsys\FrameworkBundle\Model\Product\Filter\ParameterFilterData[] $parameters
     */
    public function filterByParameters(QueryBuilder $productsQueryBuilder, array $parameters)
    {
        $parameterIndex = 1;
        $valueIndex = 1;

        foreach ($parameters as $parameterFilterData) {
            /* @var $parameterFilterData \Shopsys\FrameworkBundle\Model\Product\Filter\ParameterFilterData */

            if (count($parameterFilterData->values) === 0) {
                continue;
            }

            $parameterQueryBuilder = $this->getParameterQueryBuilder(
                $parameterFilterData,
                $productsQueryBuilder->getEntityManager(),
                $parameterIndex,
                $valueIndex
            );

            $productsQueryBuilder->andWhere($productsQueryBuilder->expr()->exists($parameterQueryBuilder));
            foreach ($parameterQueryBuilder->getParameters() as $parameter) {
                $productsQueryBuilder->setParameter($parameter->getName(), $parameter->getValue());
            }

            $parameterIndex++;
        }
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Filter\ParameterFilterData $parameterFilterData
     * @param \Doctrine\ORM\EntityManager $em
     * @param int $parameterIndex
     * @param int $valueIndex
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    private function getParameterQueryBuilder(
        ParameterFilterData $parameterFilterData,
        EntityManager $em,
        $parameterIndex,
        &$valueIndex
    ) {
        $ppvAlias = 'ppv' . $parameterIndex;
        $parameterPlaceholder = ':parameter' . $parameterIndex;

        $parameterQueryBuilder = $em->createQueryBuilder();

        $valuesExpr = $this->getValuesExpr(
            $parameterFilterData->values,
            $parameterQueryBuilder,
            $ppvAlias,
            $valueIndex
        );

        $parameterQueryBuilder
            ->select('1')
            ->from(ProductParameterValue::class, $ppvAlias)
            ->where($ppvAlias . '.product = p')
                ->andWhere($ppvAlias . '.parameter = ' . $parameterPlaceholder)
                ->andWhere($valuesExpr);

        $parameterQueryBuilder->setParameter($parameterPlaceholder, $parameterFilterData->parameter);

        return $parameterQueryBuilder;
    }

    /**
     * Generates:
     * ppv.value = :parameterValueM OR ppv.value = :parameterValueN OR ...
     *
     * @param \Shopsys\FrameworkBundle\Model\Product\Parameter\ParameterValue[] $parameterValues
     * @param \Doctrine\ORM\QueryBuilder $parameterQueryBuilder
     * @param string $ppvAlias
     * @param int $valueIndex
     * @return \Doctrine\ORM\Query\Expr
     */
    private function getValuesExpr(
        array $parameterValues,
        QueryBuilder $parameterQueryBuilder,
        $ppvAlias,
        &$valueIndex
    ) {
        $valuesExpr = $parameterQueryBuilder->expr()->orX();

        foreach ($parameterValues as $parameterValue) {
            $valuePlaceholder = ':parameterValue' . $valueIndex;

            $valuesExpr->add($ppvAlias . '.value = ' . $valuePlaceholder);
            $parameterQueryBuilder->setParameter($valuePlaceholder, $parameterValue);

            $valueIndex++;
        }

        return $valuesExpr;
    }
}
