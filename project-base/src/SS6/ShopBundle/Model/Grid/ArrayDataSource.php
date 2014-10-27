<?php

namespace SS6\ShopBundle\Model\Grid;

class ArrayDataSource implements DataSourceInterface {

	/**
	 * @var array
	 */
	private $data;

	/**
	 * @var string
	 */
	private $queryId;

	/**
	 * @param array $data
	 * @param string $queryId
	 */
	public function __construct(array $data, $queryId = null) {
		$this->data = $data;
		$this->queryId = $queryId;
	}

	/**
	 * @return string
	 */
	public function getIdQueryId() {
		return $this->queryId;
	}

	/**
	 * @param string $rowId
	 * @return mixed
	 */
	public function getOneRow($rowId) {
		if ($this->queryId === null) {
			return $this->data[$rowId];
		} else {
			foreach ($this->data as $item) {
				if ($item[$this->queryId] === $rowId) {
					return $item;
				}
			}
		}
	}

	/**
	 * @param null $limit
	 * @param int $page
	 * @param null $orderQueryId
	 * @param string $orderDirection
	 * @return array
	 * @throws \SS6\ShopBundle\Model\Grid\Exception\PaginationNotSupportedException
	 * @throws \SS6\ShopBundle\Model\Grid\Exception\OrderingNotSupportedException
	 */
	public function getRows($limit = null, $page = 1, $orderQueryId = null, $orderDirection = self::ORDER_ASC) {
		if ($limit !== null) {
			$message = 'Pagination not supported in ArrayDataSource';
			throw new \SS6\ShopBundle\Model\Grid\Exception\PaginationNotSupportedException($message);
		}

		if ($orderQueryId !== null) {
			$message = 'Ordering not supported in ArrayDataSource';
			throw new \SS6\ShopBundle\Model\Grid\Exception\OrderingNotSupportedException($message);
		}

		return $this->data;
	}

	/**
	 * @return int
	 */
	public function getTotalRowsCount() {
		return count($this->data);
	}

}
