<?php

namespace Shopsys\ShopBundle\Component\Doctrine;

use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;

class QueryBuilderService
{

    const REQUIRED_ALIASES_COUNT = 1;

    /**
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param string $class
     * @param string $alias
     * @param string $condition
     * @return \Doctrine\ORM\QueryBuilder $queryBuilder
     */
    public function addOrExtendJoin(QueryBuilder $queryBuilder, $class, $alias, $condition) {
        $rootAlias = $this->getRootAlias($queryBuilder);

        $joinAlreadyUsed = false;

        foreach ($queryBuilder->getDQLPart('join')[$rootAlias] as $join) {
            /* @var $join \Doctrine\ORM\Query\Expr\Join */
            if ($join->getJoin() === $class) {
                $joinAlreadyUsed = true;
                break;
            }
        }

        if (!$joinAlreadyUsed) {
            $queryBuilder->join(
                $class,
                $alias,
                Join::WITH,
                $condition
            );
        } else {
            $queryBuilder->andWhere($condition);
        }

        return $queryBuilder;
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @return string
     */
    private function getRootAlias(QueryBuilder $queryBuilder) {
        $rootAliases = $queryBuilder->getRootAliases();
        if (count($rootAliases) !== self::REQUIRED_ALIASES_COUNT) {
            throw new \Shopsys\ShopBundle\Component\Doctrine\Exception\InvalidCountOfAliasesException($rootAliases);
        }
        $firstAlias = reset($rootAliases);

        return $firstAlias;
    }
}
