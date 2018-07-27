<?php

namespace Shopsys\FrameworkBundle\Model\Product\Elasticsearch;

class ElasticsearchProductDataConverter
{
    /**
     * @param string $index
     * @param array $data
     * @return array
     */
    public function convertBulk(string $index, array $data): array
    {
        $result = [];
        foreach ($data as $row) {
            $result[] = [
                'index' => [
                    '_index' => $index,
                    '_type' => '_doc',
                    '_id' => (string)$row['id'],
                ],
            ];

            unset($row['id']);
            $result[] = $row;
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
