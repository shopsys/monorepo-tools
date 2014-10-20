<?php

namespace SS6\ShopBundle\Model\Grid;

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
	 * @param int $rowId
	 * @return array
	 */
	public function getOneRow($rowId);

	/**
	 * @return int
	 */
	public function getTotalRowsCount();

	/**
	 * @return string
	 */
	public function getIdQueryId();

}
