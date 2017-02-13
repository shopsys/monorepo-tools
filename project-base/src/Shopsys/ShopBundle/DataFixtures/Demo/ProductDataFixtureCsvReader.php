<?php

namespace Shopsys\ShopBundle\DataFixtures\Demo;

use Shopsys\ShopBundle\Component\Csv\CsvReader;
use Shopsys\ShopBundle\Component\String\EncodingConverter;
use Shopsys\ShopBundle\Component\String\TransformString;

class ProductDataFixtureCsvReader
{
    /**
     * @var string
     */
    private $path;

    /**
     * @var \Shopsys\ShopBundle\Component\Csv\CsvReader
     */
    private $csvReader;

    /**
     * @param string $path
     * @param \Shopsys\ShopBundle\Component\Csv\CsvReader $csvReader
     */
    public function __construct(
        $path,
        CsvReader $csvReader
    ) {
        $this->path = $path;
        $this->csvReader = $csvReader;
    }

    /**
     * @return array
     */
    public function getProductDataFixtureCsvRows()
    {
        $rawRowsWithHeader = $this->csvReader->getRowsFromCsv($this->path);
        $rawRows = array_slice($rawRowsWithHeader, 1);
        $rows = array_map(function ($rawRow) {
            return $this->prepareRawRow($rawRow);
        }, $rawRows);

        return $rows;
    }

    /**
     * @param array $rawRow
     * @return array mixed
     */
    private function prepareRawRow($rawRow)
    {
        $row = array_map([TransformString::class, 'emptyToNull'], $rawRow);

        return EncodingConverter::cp1250ToUtf8($row);
    }
}
