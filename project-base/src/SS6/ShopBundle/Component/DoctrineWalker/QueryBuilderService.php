<?php

namespace SS6\ShopBundle\Component\DoctrineWalker;

use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;

class QueryBuilderService {

	/**
	 * @param \Doctrine\ORM\QueryBuilder $queryBuilder
	 * @param string $class
	 * @param string $alias
	 * @param string $condition
	 */
	public function addOrExtendJoin(QueryBuilder $queryBuilder, $class, $alias, $condition) {
		$joinAlreadyUsed = false;
		foreach ($queryBuilder->getDQLPart('join')['p'] as $join) {
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
