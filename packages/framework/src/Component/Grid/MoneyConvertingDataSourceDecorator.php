<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Component\Grid;

use Shopsys\FrameworkBundle\Component\Money\Money;
use Shopsys\FrameworkBundle\Component\Paginator\PaginationResult;

class MoneyConvertingDataSourceDecorator implements DataSourceInterface
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Grid\DataSourceInterface
     */
    protected $innerDataSource;

    /**
     * @var array
     */
    protected $moneyColumnNames;

    /**
     * @param \Shopsys\FrameworkBundle\Component\Grid\DataSourceInterface $innerDataSource
     * @param array $moneyColumnNames
     */
    public function __construct(DataSourceInterface $innerDataSource, array $moneyColumnNames)
    {
        $this->innerDataSource = $innerDataSource;
        $this->moneyColumnNames = $moneyColumnNames;
    }

    /**
     * @param int|null $limit
     * @param int $page
     * @param string|null $orderSourceColumnName
     * @param string $orderDirection
     * @return \Shopsys\FrameworkBundle\Component\Paginator\PaginationResult
     */
    public function getPaginatedRows(
        $limit = null,
        $page = 1,
        $orderSourceColumnName = null,
        $orderDirection = self::ORDER_ASC
    ): PaginationResult {
        $paginationResult = $this->innerDataSource->getPaginatedRows($limit, $page, $orderSourceColumnName, $orderDirection);

        $results = $paginationResult->getResults();
        foreach ($results as $key => $result) {
            $results[$key] = $this->convertRow($result);
        }

        return new PaginationResult($paginationResult->getPage(), $paginationResult->getPageSize(), $paginationResult->getTotalCount(), $results);
    }

    /**
     * @param int $rowId
     * @return array
     */
    public function getOneRow($rowId): array
    {
        $row = $this->innerDataSource->getOneRow($rowId);

        return $this->convertRow($row);
    }

    /**
     * @return int
     */
    public function getTotalRowsCount(): int
    {
        return $this->innerDataSource->getTotalRowsCount();
    }

    /**
     * @return string
     */
    public function getRowIdSourceColumnName(): string
    {
        return $this->innerDataSource->getRowIdSourceColumnName();
    }

    /**
     * @param array $row
     * @return array
     */
    protected function convertRow(array $row): array
    {
        foreach ($this->moneyColumnNames as $columnName) {
            $row[$columnName] = $row[$columnName] !== null ? Money::create($row[$columnName]) : null;
        }

        return $row;
    }
}
