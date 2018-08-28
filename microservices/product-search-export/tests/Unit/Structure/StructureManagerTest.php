<?php

namespace Tests\MicroserviceProductSearchExport\Unit\Structure;

use Elasticsearch\Client;
use Elasticsearch\Namespaces\IndicesNamespace;
use PHPUnit\Framework\TestCase;
use Shopsys\MicroserviceProductSearchExport\Structure\Exception\StructureException;
use Shopsys\MicroserviceProductSearchExport\Structure\StructureManager;

class StructureManagerTest extends TestCase
{
    /**
     * @var \Shopsys\MicroserviceProductSearchExport\Structure\StructureManager
     */
    private $structureManager;

    /**
     * @var \Elasticsearch\Client
     */
    private $client;

    /**
     * @var \Elasticsearch\Namespaces\IndicesNamespace
     */
    private $indices;

    protected function setUp()
    {
        $definitionDirectory = __DIR__ . '/Resources';
        $this->client = $this->createMock(Client::class);
        $this->indices = $this->createMock(IndicesNamespace::class);
        $this->client->method('indices')->willReturn($this->indices);
        $this->structureManager = new StructureManager($definitionDirectory, $this->client);
    }

    public function testCreateSuccessIndex(): void
    {
        $expected = [
            'index' => 1,
            'body' => [
                'settings' => [
                    'index' => [
                        'number_of_shards' => 1,
                    ],
                ],
            ],
        ];
        $this->indices->expects($this->once())->method('create')->with($expected);

        $this->structureManager->createIndex(1);
    }

    public function testCreateWhileJsonNotExistsFails(): void
    {
        $this->expectException(StructureException::class);
        $this->structureManager->createIndex(3);
    }

    public function testCreateWhileJsonIsNotValidFails(): void
    {
        $this->expectException(StructureException::class);
        $this->structureManager->createIndex(0);
    }

    public function testDeleteSuccess(): void
    {
        $this->indices->method('exists')->willReturn(true);

        $expectedDelete = ['index' => 1];
        $this->indices->expects($this->once())->method('delete')->with($expectedDelete);

        $this->structureManager->deleteIndex(1);
    }

    public function testDeleteNotExisting(): void
    {
        $this->indices->method('exists')->willReturn(false);

        $this->indices->expects($this->never())->method('delete');

        $this->structureManager->deleteIndex(1);
    }
}
