<?php

namespace SS6\ShopBundle\Model\Feed;

use Doctrine\ORM\Internal\Hydration\IterableResult;
use Iterator;

abstract class AbstractDataIterator implements Iterator {

	/**
	 * @var \Doctrine\ORM\Internal\Hydration\IterableResult
	 */
	private $iterableResult;

	/**
	 * @param \Doctrine\ORM\Internal\Hydration\IterableResult $iterableResult
	 */
	public function __construct(IterableResult $iterableResult) {
		$this->iterableResult = $iterableResult;
	}

	public function rewind() {
		$this->iterableResult->rewind();
	}

	/**
	 * @return \SS6\ShopBundle\Model\Feed\Heureka\HeurekaItem
	 */
	public function next() {
		$current = $this->iterableResult->next();
		if ($current === false) {
			return false;
		}

		return $this->createItem($current);
	}

	/**
	 * @return \SS6\ShopBundle\Model\Feed\Heureka\HeurekaItem
	 */
	public function current() {
		$current = $this->iterableResult->current();
		if ($current === false) {
			return false;
		}

		return $this->createItem($current);
	}

	/**
	 * @return int
	 */
	public function key() {
		return $this->iterableResult->key();
	}

	/**
	 * @return bool
	 */
	public function valid() {
		return $this->iterableResult->valid();
	}

	/**
	 * @param array $row
	 */
	abstract protected function createItem(array $row);

}
