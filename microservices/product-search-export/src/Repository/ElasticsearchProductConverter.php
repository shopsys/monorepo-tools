<?php

namespace Shopsys\MicroserviceProductSearchExport\Repository;

class ElasticsearchProductConverter
{
    /**
     * @param string $index
     * @param array $data
     * @return array
     */
    public function convertBulk(string $index, array $data): array
    {
        $result = [];
        foreach ($data as $id => $row) {
            $result[] = [
                'index' => [
                    '_index' => $index,
                    '_type' => '_doc',
                    '_id' => (string)$id,
                ],
            ];

            $result[] = $row;
        }

        return $result;
    }
}
