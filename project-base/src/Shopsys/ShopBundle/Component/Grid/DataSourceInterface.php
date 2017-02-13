<?php

namespace Shopsys\ShopBundle\Component\Grid;

interface DataSourceInterface
{
    const ORDER_ASC = 'asc';
    const ORDER_DESC = 'desc';

    /**
     * @param int|null $limit
     * @param int $page
     * @param string|null $orderSourceColumnName
     * @param string $orderDirection
     * @return \Shopsys\ShopBundle\Component\Paginator\PaginationResult
     */
    public function getPaginatedRows(
        $limit = null,
        $page = 1,
        $orderSourceColumnName = null,
        $orderDirection = self::ORDER_ASC
    );

    /**
     * @param int $rowId
     * @return array
     */
    public function getOneRow($rowId);

    /**
     * @return int
     */
    public function getTotalRowsCount();

    /**
     * @return string
     */
    public function getRowIdSourceColumnName();
}
