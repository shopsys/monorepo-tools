<?php

namespace SS6\ShopBundle\Model\Feed;

use Doctrine\ORM\QueryBuilder;
use Iterator;

abstract class AbstractDataIterator implements Iterator {

	const BUFFER_SIZE = 500;

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
	private $itemsByPosition;

	/**
	 * @param \Doctrine\ORM\QueryBuilder $queryBuilder
	 */
	public function __construct(QueryBuilder $queryBuilder) {
		$this->queryBuilder = $queryBuilder;
		$this->rewind();
	}

	public function rewind() {
		$this->position = 0;
		$this->itemsByPosition = [];
		$this->currentItem = null;
	}

	public function next() {
		$this->position++;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Feed\Heureka\HeurekaItem
	 */
	public function current() {
		if (!array_key_exists($this->position, $this->itemsByPosition)) {
			$offset = $this->position - ($this->position % self::BUFFER_SIZE);

			$queryBuilder = clone $this->queryBuilder;
			$queryBuilder->setFirstResult($offset);
			$queryBuilder->setMaxResults(self::BUFFER_SIZE);

			$items = $this->createItems($queryBuilder->getQuery()->execute());

			$this->itemsByPosition = [];
			foreach ($items as $item) {
				$this->itemsByPosition[$offset + count($this->itemsByPosition)] = $item;
			}
		}

		if (array_key_exists($this->position, $this->itemsByPosition)) {
			$this->currentItem = $this->itemsByPosition[$this->position];
		} else {
			$this->currentItem = false;
		}

		return $this->currentItem;
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
	 * @param \SS6\ShopBundle\Model\Product\Product[] $products
	 * @return array
	 */
	abstract protected function createItems(array $products);

}
