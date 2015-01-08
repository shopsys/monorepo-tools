<?php

namespace SS6\ShopBundle\Component\DoctrineWalker;

use Doctrine\ORM\Query\SqlWalker;
use SS6\ShopBundle\Model\Grid\DataSourceInterface;

/**
 * Allows ORDER BY using NULLS FIRST | LAST
 * Inspired by https://github.com/beberlei/DoctrineExtensions/blob/master/lib/DoctrineExtensions/Query/SortableNullsWalker.php
 */
class SortableNullsWalker extends SqlWalker {

	const NULLS_FIRST = 'NULLS FIRST';
	const NULLS_LAST = 'NULLS LAST';

	public function walkOrderByItem($orderByItem) {
		$sql = parent::walkOrderByItem($orderByItem);

		$orderDirection = strtolower($orderByItem->type);

		$nullsOrder = null;
		if ($orderDirection === DataSourceInterface::ORDER_ASC) {
			$nullsOrder = self::NULLS_FIRST;
		} elseif ($orderDirection === DataSourceInterface::ORDER_DESC) {
			$nullsOrder = self::NULLS_LAST;
		}

		if ($nullsOrder !== null) {
			$sql .= ' ' . $nullsOrder;
		}

		return $sql;
	}

}
