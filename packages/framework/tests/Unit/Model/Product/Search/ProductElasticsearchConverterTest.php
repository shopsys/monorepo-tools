<?php

namespace Tests\FrameworkBundle\Unit\Model\Product\Search;

use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Model\Product\Search\ProductElasticsearchConverter;

class ProductElasticsearchConverterTest extends TestCase
{
    public function testConvertBulk(): void
    {
        $translator = new ProductElasticsearchConverter();

        $data = $this->createConvertBulkData();
        $expected = $this->createConvertBulkExpected();

        $result = $translator->convertBulk('1', $data);
        $this->assertSame($expected, $result);
    }

    /**
     * @return array
     */
    private function createConvertBulkData(): array
    {
        return [
            3 => [
                'name' => '47" LG 47LA790V (FHD)',
                'catnum' => '5965879P',
                'partno' => '47LA790V',
                'ean' => '8845781245928',
                'description' => 'At first glance its <strong> beautiful design </strong>',
                'short_description' => '47 "LG 47LA790V Luxury TV from the South Korean company LG bears 47LA790S',
            ],
            2 => [
                'name' => '47" LG 47LA790V',
                'catnum' => '5965',
                'partno' => '47LA',
                'ean' => '8845781',
                'description' => 'At first glance its beautiful',
                'short_description' => '47 "LG 47LA790V Luxury TV',
            ],
        ];
    }

    /**
     * @return array
     */
    private function createConvertBulkExpected(): array
    {
        return [
            [
                'index' => [
                    '_index' => '1',
                    '_type' => '_doc',
                    '_id' => '3',
                ],
            ],
            [
                'name' => '47" LG 47LA790V (FHD)',
                'catnum' => '5965879P',
                'partno' => '47LA790V',
                'ean' => '8845781245928',
                'description' => 'At first glance its <strong> beautiful design </strong>',
                'short_description' => '47 "LG 47LA790V Luxury TV from the South Korean company LG bears 47LA790S',
            ],
            [
                'index' => [
                    '_index' => '1',
                    '_type' => '_doc',
                    '_id' => '2',
                ],
            ],
            [
                'name' => '47" LG 47LA790V',
                'catnum' => '5965',
                'partno' => '47LA',
                'ean' => '8845781',
                'description' => 'At first glance its beautiful',
                'short_description' => '47 "LG 47LA790V Luxury TV',
            ],
        ];
    }

    public function testFillEmptyFields(): void
    {
        $product = [
            'name' => '47" LG 47LA790V (FHD)',
            'catnum' => '5965879P',
            'partno' => '47LA790V',
            'ean' => '8845781245928',
            'description' => 'At first glance its <strong> beautiful design </strong>',
            'short_description' => '47 "LG 47LA790V Luxury TV from the South Korean company LG bears 47LA790S',
        ];

        $expected = [
            'name' => '47" LG 47LA790V (FHD)',
            'catnum' => '5965879P',
            'partno' => '47LA790V',
            'ean' => '8845781245928',
            'description' => 'At first glance its <strong> beautiful design </strong>',
            'short_description' => '47 "LG 47LA790V Luxury TV from the South Korean company LG bears 47LA790S',
            'availability' => '',
            'detail_url' => '',
            'categories' => [],
            'flags' => [],
            'parameters' => [],
            'prices' => [],
            'visibility' => [],
            'ordering_priority' => 0,
            'in_stock' => false,
            'main_variant' => false,
            'calculated_selling_denied' => true,
            'selling_denied' => true,
            'brand' => null,
        ];

        $converter = new ProductElasticsearchConverter();
        $this->assertSame($expected, $converter->fillEmptyFields($product));
    }
}
