<?php

namespace SS6\ShopBundle\Model\Grid;

use Doctrine\ORM\QueryBuilder;
use SS6\ShopBundle\Component\Paginator\QueryPaginator;
use SS6\ShopBundle\Model\Grid\DataSourceInterface;

class QueryBuilderDataSource implements DataSourceInterface {

	const HYDRATION_MODE = 'GroupedScalarHydrator';

	/**
	 * @var \Doctrine\ORM\QueryBuilder
	 */
	private $queryBuilder;

	/**
	 * @var string
	 */
	private $queryId;

	/**
	 * @param \Doctrine\ORM\QueryBuilder $queryBuilder
	 * @param string $queryId
	 */
	public function __construct(QueryBuilder $queryBuilder, $queryId) {
		$this->queryBuilder = $queryBuilder;
		$this->queryId = $queryId;
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
		if ($orderQueryId !== null) {
			$this->addQueryOrder($queryBuilder, $orderQueryId, $orderDirection);
		}

		$queryPaginator = new QueryPaginator($queryBuilder, self::HYDRATION_MODE);

		$paginationResult = $queryPaginator->getResult($page, $limit);
		/* @var $paginationResult \SS6\ShopBundle\Component\Paginator\PaginationResult */

		return $paginationResult->getResults();
	}

	/**
	 * @param int $rowId
	 * @return array
	 */
	public function getOneRow($rowId) {
		$queryBuilder = clone $this->queryBuilder;
		$this->prepareQueryWithOneRow($queryBuilder, $rowId);

		return $queryBuilder->getQuery()->getSingleResult('GroupedScalarHydrator');
	}

	/**
	 * @return int
	 */
	public function getTotalRowsCount() {
		$queryPaginator = new QueryPaginator($this->queryBuilder, self::HYDRATION_MODE);
		return $queryPaginator->getTotalCount();
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
	 * @param \Doctrine\ORM\QueryBuilder $queryBuilder
	 * @param int $rowId
	 */
	private function prepareQueryWithOneRow(QueryBuilder $queryBuilder, $rowId) {
		$queryBuilder
			->andWhere($this->queryId . ' = :rowId')
			->setParameter('rowId', $rowId)
			->setFirstResult(null)
			->setMaxResults(null)
			->resetDQLPart('orderBy');
	}

	/**
	 * @return string
	 */
	public function getIdQueryId() {
		return $this->queryId;
	}

}
