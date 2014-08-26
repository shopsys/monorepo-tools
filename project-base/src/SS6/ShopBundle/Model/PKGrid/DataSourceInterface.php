<?php

namespace SS6\ShopBundle\Model\PKGrid;

interface DataSourceInterface {
	
	const ORDER_ASC = 'asc';
	const ORDER_DESC = 'desc';

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
	);

	/**
	 * @param string $queryId
	 * @param int $rowId
	 */
	public function getRowsWithOneRow($queryId, $rowId);

	/**
	 * @return int
	 */
	public function getTotalRowsCount();
	
}
