<?php

namespace Shopsys\ShopBundle\Component\Grid;

use Shopsys\ShopBundle\Component\Paginator\PaginationResult;

class ArrayDataSource implements DataSourceInterface
{
    /**
     * @var array
     */
    private $data;

    /**
     * @var string
     */
    private $rowIdSourceColumnName;

    /**
     * @param array $data
     * @param string $rowIdSourceColumnName
     */
    public function __construct(array $data, $rowIdSourceColumnName = null)
    {
        $this->data = $data;
        $this->rowIdSourceColumnName = $rowIdSourceColumnName;
    }

    /**
     * @return string
     */
    public function getRowIdSourceColumnName()
    {
        return $this->rowIdSourceColumnName;
    }

    /**
     * @param string $rowId
     * @return mixed
     */
    public function getOneRow($rowId)
    {
        if ($this->rowIdSourceColumnName === null) {
            return $this->data[$rowId];
        } else {
            foreach ($this->data as $item) {
                if ($item[$this->rowIdSourceColumnName] === $rowId) {
                    return $item;
                }
            }
        }
    }

    /**
     * @param null $limit
     * @param int $page
     * @param null $orderSourceColumnName
     * @param string $orderDirection
     * @return \Shopsys\ShopBundle\Component\Paginator\PaginationResult
     */
    public function getPaginatedRows($limit = null, $page = 1, $orderSourceColumnName = null, $orderDirection = self::ORDER_ASC)
    {
        if ($limit !== null) {
            $message = 'Pagination not supported in ArrayDataSource';
            throw new \Shopsys\ShopBundle\Component\Grid\Exception\PaginationNotSupportedException($message);
        }

        if ($orderSourceColumnName !== null) {
            $message = 'Ordering not supported in ArrayDataSource';
            throw new \Shopsys\ShopBundle\Component\Grid\Exception\OrderingNotSupportedException($message);
        }

        return new PaginationResult(1, count($this->data), count($this->data), $this->data);
    }

    /**
     * @return int
     */
    public function getTotalRowsCount()
    {
        return count($this->data);
    }
}
