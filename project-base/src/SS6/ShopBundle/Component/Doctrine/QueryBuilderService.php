<?php

namespace SS6\ShopBundle\Component\Doctrine;

use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;

class QueryBuilderService {

	const REQUIRED_ALIASES_COUNT = 1;
	/**
	 * @param \Doctrine\ORM\QueryBuilder $queryBuilder
	 * @param string $class
	 * @param string $alias
	 * @param string $condition
	 */
	public function addOrExtendJoin(QueryBuilder $queryBuilder, $class, $alias, $condition) {
		$joinAlreadyUsed = false;
		$rootAliases = $queryBuilder->getRootAliases();
		if (count($rootAliases) !== self::REQUIRED_ALIASES_COUNT) {
			throw new SS6\ShopBundle\Component\Doctrine\Exception\InvalidCountOfAliasesException($rootAliases);
		}
		$firstAlias = reset($rootAliases);
		foreach ($queryBuilder->getDQLPart('join')[$firstAlias] as $join) {
			/* @var $join \Doctrine\ORM\Query\Expr\Join */
			if ($join->getJoin() === $class) {
				$joinAlreadyUsed = true;
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
	}
}
