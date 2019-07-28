<?php

namespace Shopsys\FrameworkBundle\Model\Product\Filter;

use Doctrine\ORM\EntityManagerInterface;
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

        foreach ($parameters as $parameterFilterData) {
            /* @var $parameterFilterData \Shopsys\FrameworkBundle\Model\Product\Filter\ParameterFilterData */

            if (count($parameterFilterData->values) === 0) {
                continue;
            }

            $parameterQueryBuilder = $this->getParameterQueryBuilder(
                $parameterFilterData,
                $productsQueryBuilder->getEntityManager(),
                $parameterIndex
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
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @param int $parameterIndex
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function getParameterQueryBuilder(
        ParameterFilterData $parameterFilterData,
        EntityManagerInterface $em,
        $parameterIndex
    ) {
        $ppvAlias = 'ppv' . $parameterIndex;
        $parameterPlaceholder = ':parameter' . $parameterIndex;

        $parameterQueryBuilder = $em->createQueryBuilder();

        $valuesExpr = $this->getValuesExpr(
            $parameterFilterData->values,
            $parameterQueryBuilder,
            $ppvAlias,
            $parameterIndex
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
     * ppv.value = :parameterValueX_M OR ppv.value = :parameterValueX_N OR ...
     *
     * @param \Shopsys\FrameworkBundle\Model\Product\Parameter\ParameterValue[] $parameterValues
     * @param \Doctrine\ORM\QueryBuilder $parameterQueryBuilder
     * @param string $ppvAlias
     * @param int $parameterIndex
     * @return \Doctrine\ORM\Query\Expr\Orx
     */
    protected function getValuesExpr(
        array $parameterValues,
        QueryBuilder $parameterQueryBuilder,
        $ppvAlias,
        $parameterIndex
    ) {
        $valuesExpr = $parameterQueryBuilder->expr()->orX();

        $valueIndex = 1;
        foreach ($parameterValues as $parameterValue) {
            $valuePlaceholder = ':parameterValue' . $parameterIndex . '_' . $valueIndex;

            $valuesExpr->add($ppvAlias . '.value = ' . $valuePlaceholder);
            $parameterQueryBuilder->setParameter($valuePlaceholder, $parameterValue);

            $valueIndex++;
        }

        return $valuesExpr;
    }
}
