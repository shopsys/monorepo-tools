<?php

namespace SS6\ShopBundle\Model\Feed;

use Doctrine\ORM\QueryBuilder;
use Iterator;

abstract class AbstractDataIterator implements Iterator {

	const ITEMS_BUFFER_SIZE = 100;

	/**
	 * @var int
	 */
	private $position;

	/**
	 * @var array|null
	 */
	private $currentItem;

	/**
	 * @var \Doctrine\ORM\QueryBuilder
	 */
	private $queryBuilder;

	/**
	 * @var array
	 */
	private $itemsBuffer;

	/**
	 * @param \Doctrine\ORM\QueryBuilder $queryBuilder
	 */
	public function __construct(QueryBuilder $queryBuilder) {
		$this->queryBuilder = $queryBuilder;
		$this->rewind();
	}

	public function rewind() {
		$this->position = 0;
		$this->itemsBuffer = [];
		$this->currentItem = null;
	}

	public function next() {
		$this->position++;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Feed\Heureka\HeurekaItem
	 */
	public function current() {
		if (!array_key_exists($this->position, $this->itemsBuffer)) {
			$offset = $this->position - ($this->position % self::ITEMS_BUFFER_SIZE);

			$queryBuilder = clone $this->queryBuilder;
			$queryBuilder->setFirstResult($offset);
			$queryBuilder->setMaxResults(self::ITEMS_BUFFER_SIZE);

			$this->itemsBuffer = [];
			foreach ($queryBuilder->getQuery()->execute() as $queryPosition => $item) {
				$this->itemsBuffer[$offset + $queryPosition] = $item;
			}
		}

		if (array_key_exists($this->position, $this->itemsBuffer)) {
			$this->currentItem = $this->itemsBuffer[$this->position];
			return $this->createItem([$this->currentItem]);
		} else {
			$this->currentItem = false;
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

}
