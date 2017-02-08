<?php

namespace SS6\ShopBundle\Component\Paginator;

use Doctrine\DBAL\SQLParserUtils;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\QueryBuilder;
use SS6\ShopBundle\Component\Doctrine\SortableNullsWalker;

class QueryPaginator implements PaginatorInterface {

	/**
	 * @var \Doctrine\ORM\QueryBuilder
	 */
	private $queryBuilder;

	/**
	 * @var string|null
	 */
	private $hydrationMode;

	/**
	 * @param \Doctrine\ORM\QueryBuilder $queryBuilder
	 * @param string|null $hydrationMode
	 */
	public function __construct(QueryBuilder $queryBuilder, $hydrationMode = null) {
		$this->queryBuilder = $queryBuilder;
		$this->hydrationMode = $hydrationMode;
	}

	/**
	 * @param int $page
	 * @param int $pageSize
	 * @return \SS6\ShopBundle\Component\Paginator\PaginationResult
	 */
	public function getResult($page = 1, $pageSize = null) {
		$queryBuilder = clone $this->queryBuilder;

		if ($page < 1) {
			$page = 1;
		}

		$totalCount = $this->getTotalCount();

		if ($pageSize !== null) {
			$maxPage = (int)ceil($totalCount / $pageSize);
			if ($maxPage < 1) {
				$maxPage = 1;
			}

			if ($page > $maxPage) {
				$page = $maxPage;
			}

			$queryBuilder
				->setFirstResult($pageSize * ($page - 1))
				->setMaxResults($pageSize);
		}

		$query = $queryBuilder->getQuery();
		$query->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, SortableNullsWalker::class);

		$results = $query->execute(null, $this->hydrationMode);

		return new PaginationResult($page, $pageSize, $totalCount, $results);

	}

	/**
	 * @return int
	 */
	public function getTotalCount() {
		$totalNativeQuery = $this->getTotalNativeQuery($this->queryBuilder);

		return $totalNativeQuery->getSingleScalarResult();
	}

	/**
	 * @param \Doctrine\ORM\QueryBuilder $queryBuilder
	 * @return \Doctrine\ORM\NativeQuery
	 */
	private function getTotalNativeQuery(QueryBuilder $queryBuilder) {
		$em = $queryBuilder->getEntityManager();

		$totalQueryBuilder = clone $queryBuilder;
		$totalQueryBuilder
			->setFirstResult(null)
			->setMaxResults(null)
			->resetDQLPart('orderBy');

		$query = $totalQueryBuilder->getQuery();

		$parametersAssoc = [];
		foreach ($query->getParameters() as $parameter) {
			$parametersAssoc[$parameter->getName()] = $parameter->getValue();
		}

		list(, $flatenedParameters) = SQLParserUtils::expandListParameters(
			$query->getDQL(),
			$parametersAssoc,
			[]
		);

		$sql = 'SELECT COUNT(*)::INTEGER AS total_count FROM (' . $query->getSQL() . ') ORIGINAL_QUERY';

		$rsm = new ResultSetMapping();
		$rsm->addScalarResult('total_count', 'totalCount');
		return $em->createNativeQuery($sql, $rsm)
			->setParameters($flatenedParameters);
	}

}
