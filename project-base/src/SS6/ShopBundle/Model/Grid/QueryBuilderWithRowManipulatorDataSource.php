<?php

namespace SS6\ShopBundle\Model\Grid;

use Doctrine\ORM\QueryBuilder;
use SS6\ShopBundle\Component\Paginator\PaginationResult;

class QueryBuilderWithRowManipulatorDataSource extends QueryBuilderDataSource {

	/**
	 * @var callable
	 */
	private $manipulateRowCallback;

	/**
	 * @param \Doctrine\ORM\QueryBuilder $queryBuilder
	 * @param string $queryId
	 * @param callable $manipulateRowCallback
	 */
	public function __construct(QueryBuilder $queryBuilder, $queryId, callable $manipulateRowCallback) {
		parent::__construct($queryBuilder, $queryId);
		$this->manipulateRowCallback = $manipulateRowCallback;
	}

	/**
	 * @param callable $manipulateRowCallback
	 */
	public function setManipulateRowCallback(callable $manipulateRowCallback) {
		$this->manipulateRowCallback = $manipulateRowCallback;
	}

	/**
	 * @param int $rowId
	 * @return array
	 */
	public function getOneRow($rowId) {
		$row = parent::getOneRow($rowId);
		return call_user_func($this->manipulateRowCallback, $row);
	}

	/**
	 * @param int|null $limit
	 * @param int $page
	 * @param string|null $orderQueryId
	 * @param string $orderDirection
	 * @return array
	 */
	public function getPaginatedRows($limit = null, $page = 1, $orderQueryId = null, $orderDirection = self::ORDER_ASC) {
		$parentPaginatedResult = parent::getPaginatedRows($limit, $page, $orderQueryId, $orderDirection);
		$results = array_map($this->manipulateRowCallback, $parentPaginatedResult->getResults());
		return new PaginationResult($page, $limit, count($results), $results);
	}

}
