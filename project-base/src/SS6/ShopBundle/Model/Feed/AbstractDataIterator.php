<?php

namespace SS6\ShopBundle\Model\Feed;

use Doctrine\ORM\QueryBuilder;
use Iterator;

abstract class AbstractDataIterator implements Iterator {

	const BUFFER_SIZE = 100;

	/**
	 * @var int
	 */
	private $position;

	/**
	 * @var array|null
	 */
	private $currentRowWithData;

	/**
	 * @var \Doctrine\ORM\QueryBuilder
	 */
	private $queryBuilder;

	/**
	 * @var array
	 */
	private $rowsWithDataBuffer;

	/**
	 * @param \Doctrine\ORM\QueryBuilder $queryBuilder
	 */
	public function __construct(QueryBuilder $queryBuilder) {
		$this->queryBuilder = $queryBuilder;
		$this->rewind();
	}

	public function rewind() {
		$this->position = 0;
		$this->rowsWithDataBuffer = [];
		$this->currentRowWithData = null;
	}

	public function next() {
		$this->position++;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Feed\Heureka\HeurekaItem
	 */
	public function current() {
		if (!array_key_exists($this->position, $this->rowsWithDataBuffer)) {
			$offset = $this->position - ($this->position % self::BUFFER_SIZE);

			$queryBuilder = clone $this->queryBuilder;
			$queryBuilder->setFirstResult($offset);
			$queryBuilder->setMaxResults(self::BUFFER_SIZE);

			$rowsWithData = $this->loadOtherData($queryBuilder->getQuery()->execute());

			$this->rowsWithDataBuffer = [];
			foreach ($rowsWithData as $rowWithData) {
				$this->rowsWithDataBuffer[$offset + count($this->rowsWithDataBuffer)] = $rowWithData;
			}
		}

		if (array_key_exists($this->position, $this->rowsWithDataBuffer)) {
			$this->currentRowWithData = $this->rowsWithDataBuffer[$this->position];
			return $this->createItem($this->currentRowWithData);
		} else {
			$this->currentRowWithData = false;
			return false;
		}
	}

	/**
	 * @return int
	 */
	public function key() {
		return $this->position;
	}

	/**
	 * @return bool
	 */
	public function valid() {
		return $this->current() !== false;
	}

	/**
	 * @param array $row
	 */
	abstract protected function createItem(array $row);

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product[] $products
	 * @return array
	 */
	abstract protected function loadOtherData(array $products);

}
