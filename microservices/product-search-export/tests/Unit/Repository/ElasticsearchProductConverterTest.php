<?php

namespace Tests\MicroserviceProductSearchExport\Unit\Repository;

use PHPUnit\Framework\TestCase;
use Shopsys\MicroserviceProductSearchExport\Repository\ElasticsearchProductConverter;

class ElasticsearchProductConverterTest extends TestCase
{
    public function testConvertBulk(): void
    {
        $translator = new ElasticsearchProductConverter();

        $data = $this->createData();
        $expected = $this->createExpected();

        $result = $translator->convertBulk('1', $data);
        $this->assertSame($expected, $result);
    }

    /**
     * @return array
     */
    private function createData(): array
    {
        return [
            3 => [
                'name' => '47" LG 47LA790V (FHD)',
                'catnum' => '5965879P',
                'partno' => '47LA790V',
                'ean' => '8845781245928',
                'description' => 'At first glance its <strong> beautiful design </strong>',
                'shortDescription' => '47 "LG 47LA790V Luxury TV from the South Korean company LG bears 47LA790S',
            ],
            2 => [
                'name' => '47" LG 47LA790V',
                'catnum' => '5965',
                'partno' => '47LA',
                'ean' => '8845781',
                'description' => 'At first glance its beautiful',
                'shortDescription' => '47 "LG 47LA790V Luxury TV',
            ],
        ];
    }

    /**
     * @return array
     */
    private function createExpected(): array
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
                'shortDescription' => '47 "LG 47LA790V Luxury TV from the South Korean company LG bears 47LA790S',
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
                'shortDescription' => '47 "LG 47LA790V Luxury TV',
            ],
        ];
    }
}
