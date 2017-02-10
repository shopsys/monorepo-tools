<?php

namespace Shopsys\ShopBundle\Component\Doctrine;

use Doctrine\ORM\Query\SqlWalker;

/**
 * Allows ORDER BY using NULLS FIRST | LAST
 * Inspired by https://github.com/beberlei/DoctrineExtensions/blob/master/lib/DoctrineExtensions/Query/SortableNullsWalker.php
 */
class SortableNullsWalker extends SqlWalker
{

    const NULLS_FIRST = 'NULLS FIRST';
    const NULLS_LAST = 'NULLS LAST';

    /**
     * @param \Doctrine\ORM\Query\AST\OrderByItem $orderByItem
     * @return string
     */
    public function walkOrderByItem($orderByItem) {
        $sql = parent::walkOrderByItem($orderByItem);

        if ($orderByItem->isAsc()) {
            $sql .= ' ' . self::NULLS_FIRST;
        } elseif ($orderByItem->isDesc()) {
            $sql .= ' ' . self::NULLS_LAST;
        }

        return $sql;
    }

}
