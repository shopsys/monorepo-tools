<?php

namespace SS6\ShopBundle\Component\Paginator;

class PaginationResult {

	/**
	 * @var int
	 */
	private $page;

	/**
	 * @var int
	 */
	private $pageSize;

	/**
	 * @var int
	 */
	private $totalCount;

	/**
	 * @var array
	 */
	private $results;

	/**
	 * @var int
	 */
	private $pageCount;

	/**
	 * @param int $page
	 * @param int $pageSize
	 * @param int $totalCount
	 * @param array $results
	 */
	public function __construct($page, $pageSize, $totalCount, $results) {
		$this->page = $page;
		$this->pageSize = $pageSize;
		$this->totalCount = $totalCount;
		$this->results = $results;
		if ($pageSize === null) {
			$this->pageCount = 1;
		} else {
			$this->pageCount = (int)ceil($this->totalCount / $this->pageSize);
		}

	}

	/**
	 * @return int
	 */
	public function getPage() {
		return $this->page;
	}

	/**
	 * @return int
	 */
	public function getPageSize() {
		return $this->pageSize;
	}

	/**
	 * @return int
	 */
	public function getTotalCount() {
		return $this->totalCount;
	}

	/**
	 * @return array
	 */
	public function getResults() {
		return $this->results;
	}

	/**
	 * @return int
	 */
	public function getPageCount() {
		return $this->pageCount;
	}

}