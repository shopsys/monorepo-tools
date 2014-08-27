<?php

namespace SS6\ShopBundle\Model\PKGrid;

use Doctrine\DBAL\SQLParserUtils;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMapping;
use SS6\ShopBundle\Model\PKGrid\DataSourceInterface;

class QueryBuilderDataSource implements DataSourceInterface {

	/**
	 * @var \Doctrine\ORM\QueryBuilder
	 */
	private $queryBuilder;

	/**
	 * @param \Doctrine\ORM\QueryBuilder $queryBuilder
	 */
	public function __construct(QueryBuilder $queryBuilder) {
		$this->queryBuilder = $queryBuilder;
	}

	/**
	 * @param int|null $limit
	 * @param int $page
	 * @param string|null $orderQueryId
	 * @param string $orderDirection
	 * @return array
	 */
	public function getRows(
		$limit = null,
		$page = 1,
		$orderQueryId = null,
		$orderDirection = self::ORDER_ASC
	) {
		$queryBuilder = clone $this->queryBuilder;
		$this->prepareQuery($queryBuilder, $limit, $page, $orderQueryId, $orderDirection);

		return $queryBuilder->getQuery()->execute(null, 'GroupedScalarHydrator');
	}

	/**
	 * @param string $queryId
	 * @param int $rowId
	 */
	public function getOneRow($queryId, $rowId) {
		$queryBuilder = clone $this->queryBuilder;
		$this->prepareQueryWithOneRow($queryBuilder, $queryId, $rowId);

		return $queryBuilder->getQuery()->getSingleResult('GroupedScalarHydrator');
	}

	/**
	 * @return int
	 */
	public function getTotalRowsCount() {
		$totalNativeQuery = $this->getTotalNativeQuery($this->queryBuilder);

		return $totalNativeQuery->getSingleScalarResult();
	}

	/**
	 * @param \Doctrine\ORM\QueryBuilder $queryBuilder
	 * @param int $limit
	 * @param int $page
	 */
	private function addQueryLimit(QueryBuilder $queryBuilder, $limit, $page) {
		$queryBuilder
			->setFirstResult($limit * ($page - 1))
			->setMaxResults($limit);
	}

	/**
	 * @param \Doctrine\ORM\QueryBuilder $queryBuilder
	 * @param string $orderQueryId
	 * @param string $orderDirection
	 */
	private function addQueryOrder(QueryBuilder $queryBuilder, $orderQueryId, $orderDirection) {
		$queryBuilder->orderBy($orderQueryId, $orderDirection);
	}

	/**
	 *
	 * @param \Doctrine\ORM\QueryBuilder $queryBuilder
	 * @param int|null $limit
	 * @param int $page
	 * @param string|null $orderQueryId
	 * @param string $orderDirection
	 */
	private function prepareQuery(
		QueryBuilder $queryBuilder,
		$limit = null,
		$page = 1,
		$orderQueryId = null,
		$orderDirection = self::ORDER_ASC
	) {
		if ($limit !== null) {
			$this->addQueryLimit($queryBuilder, $limit, $page);
		}
		if ($orderQueryId !== null) {
			$this->addQueryOrder($queryBuilder, $orderQueryId, $orderDirection);
		}
	}

	/**
	 * @param \Doctrine\ORM\QueryBuilder $queryBuilder
	 * @param string $queryId
	 * @param int $rowId
	 */
	private function prepareQueryWithOneRow(QueryBuilder $queryBuilder, $queryId, $rowId) {
		$queryBuilder
			->andWhere($queryId . ' = :rowId')
			->setParameter('rowId', $rowId)
			->setFirstResult(null)
			->setMaxResults(null)
			->resetDQLPart('orderBy');
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
