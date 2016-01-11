<?php

namespace SS6\ShopBundle\Model\Feed;

use Doctrine\ORM\QueryBuilder;
use Iterator;
use SS6\ShopBundle\Component\Domain\Config\DomainConfig;
use SS6\ShopBundle\Model\Feed\FeedItemFactoryInterface;

class FeedItemIterator implements Iterator {

	const BUFFER_SIZE = 100;

	/**
	 * @var int
	 */
	private $position;

	/**
	 * @var mixed|bool|null
	 */
	private $currentItem;

	/**
	 * @var \Doctrine\ORM\QueryBuilder
	 */
	private $queryBuilder;

	/**
	 * @var \SS6\ShopBundle\Model\Feed\FeedItemFactoryInterface
	 */
	private $feedItemFactory;

	/**
	 * @var \SS6\ShopBundle\Component\Domain\Config\DomainConfig
	 */
	private $domainConfig;

	/**
	 * @var array
	 */
	private $itemsByPosition;

	/**
	 * @var int|null
	 */
	private $feedItemIdToContinue;

	/**
	 * @param \Doctrine\ORM\QueryBuilder $queryBuilder
	 * @param \SS6\ShopBundle\Model\Feed\FeedItemFactoryInterface $feedItemFactory
	 * @param \SS6\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
	 */
	public function __construct(
		QueryBuilder $queryBuilder,
		FeedItemFactoryInterface $feedItemFactory,
		DomainConfig $domainConfig
	) {
		$this->queryBuilder = $queryBuilder;
		$this->feedItemFactory = $feedItemFactory;
		$this->domainConfig = $domainConfig;
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
	 * @return mixed
	 */
	public function current() {
		if (!array_key_exists($this->position, $this->itemsByPosition)) {
			$offset = $this->position - ($this->position % self::BUFFER_SIZE);

			$queryBuilder = clone $this->queryBuilder;
			$queryBuilder->orderBy('p.id');
			if ($this->feedItemIdToContinue !== null) {
				$queryBuilder
					->andWhere('p.id > :feedItemIdToContinue')
					->setParameter('feedItemIdToContinue', $this->feedItemIdToContinue);
			}
			$queryBuilder->setFirstResult($offset);
			$queryBuilder->setMaxResults(self::BUFFER_SIZE);

			$items = $this->feedItemFactory->createItems($queryBuilder->getQuery()->execute(), $this->domainConfig);

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
	 * @param int|null $feedItemIdToContinue
	 */
	public function setFeedItemIdToContinue($feedItemIdToContinue) {
		$this->feedItemIdToContinue = $feedItemIdToContinue;
		$this->rewind();
	}

}
