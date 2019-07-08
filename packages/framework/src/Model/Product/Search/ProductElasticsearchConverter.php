<?php

namespace Shopsys\FrameworkBundle\Model\Product\Search;

class ProductElasticsearchConverter
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

    /**
     * @param array $data
     * @return array
     */
    public function convertExportBulk(array $data): array
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

    /**
     * @param array $product
     * @return array
     */
    public function fillEmptyFields(array $product): array
    {
        $result = $product;

        $result['availability'] = $product['availability'] ?? '';
        $result['catnum'] = $product['catnum'] ?? '';
        $result['description'] = $product['description'] ?? '';
        $result['detail_url'] = $product['detail_url'] ?? '';
        $result['ean'] = $product['ean'] ?? '';
        $result['name'] = $product['name'] ?? '';
        $result['partno'] = $product['partno'] ?? '';
        $result['short_description'] = $product['short_description'] ?? '';

        $result['categories'] = $product['categories'] ?? [];
        $result['flags'] = $product['flags'] ?? [];
        $result['parameters'] = $product['parameters'] ?? [];
        $result['prices'] = $product['prices'] ?? [];
        $result['visibility'] = $product['visibility'] ?? [];

        $result['ordering_priority'] = $product['ordering_priority'] ?? 0;

        $result['in_stock'] = $product['in_stock'] ?? false;
        $result['main_variant'] = $product['main_variant'] ?? false;

        $result['calculated_selling_denied'] = $product['calculated_selling_denied'] ?? true;
        $result['selling_denied'] = $product['selling_denied'] ?? true;

        // unknown default value, used for filtering only
        $result['brand'] = $product['brand'] ?? null;

        return $result;
    }
}
