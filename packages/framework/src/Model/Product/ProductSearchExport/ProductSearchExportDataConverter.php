<?php

namespace Shopsys\FrameworkBundle\Model\Product\ProductSearchExport;

class ProductSearchExportDataConverter
{
    /**
     * @param array $data
     * @return array
     */
    public function convertBulk(array $data): array
    {
        $result = [];
        foreach ($data as $row) {
            $id = (string)$row['id'];
            unset($row['id']);
            $result[$id] = $row;
        }

        return $result;
    }

    /**
     * @param array $data
     * @return int[]
     */
    public function extractIds(array $data): array
    {
        return array_column($data, 'id');
    }
}
