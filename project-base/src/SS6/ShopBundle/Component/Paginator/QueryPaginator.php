<?php

namespace SS6\ShopBundle\Component\Paginator;

use Doctrine\DBAL\SQLParserUtils;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\QueryBuilder;

class QueryPaginator implements PaginatorInterface{

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
	 * @param int $limit
	 * @return \SS6\ShopBundle\Component\Paginator\PaginationResult
	 */
	public function getResult($page = 1, $limit = null) {
		if ($limit !== null) {
			$this->queryBuilder
			->setFirstResult($limit * ($page - 1))
			->setMaxResults($limit);
		}

		$results = $this->queryBuilder->getQuery()->execute(null, $this->hydrationMode);
		$totalCount = $this->getTotalCount();

		return new PaginationResult($page, $limit, $totalCount, $results);

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

		$parametersAssoc = array();
		foreach ($query->getParameters() as $parameter) {
			$parametersAssoc[$parameter->getName()] = $parameter->getValue();
		}

		list($dummyQuery, $flatenedParameters) = SQLParserUtils::expandListParameters(
			$query->getDQL(),
			$parametersAssoc,
			array()
		);

		$sql = 'SELECT COUNT(*) AS total_count FROM (' . $query->getSQL() . ') ORIGINAL_QUERY';

		$rsm = new ResultSetMapping();
		$rsm->addScalarResult('total_count', 'totalCount');
		return $em->createNativeQuery($sql, $rsm)
			->setParameters($flatenedParameters);
	}

}
