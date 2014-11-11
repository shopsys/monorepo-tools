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
	private $limit;

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
	 * @param int $limit
	 * @param int $totalCount
	 * @param array $results
	 */
	public function __construct($page, $limit, $totalCount, $results) {
		$this->page = $page;
		$this->limit = $limit;
		$this->totalCount = $totalCount;
		$this->results = $results;
		if ($limit === null) {
			$this->pageCount = 1;
		} else {
			$this->pageCount = round($this->totalCount/$this->limit, 0, PHP_ROUND_HALF_UP);
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
	public function getLimit() {
		return $this->limit;
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