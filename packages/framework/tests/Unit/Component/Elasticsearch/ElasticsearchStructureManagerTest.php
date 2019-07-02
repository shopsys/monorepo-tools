<?php

namespace Tests\FrameworkBundle\Unit\Component\Elasticsearch;

use Elasticsearch\Client;
use Elasticsearch\Namespaces\IndicesNamespace;
use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Component\Elasticsearch\ElasticsearchStructureManager;
use Shopsys\FrameworkBundle\Component\Elasticsearch\Exception\ElasticsearchStructureException;

class ElasticsearchStructureManagerTest extends TestCase
{
    protected const ELASTICSEARCH_INDEX = 'test-product';

    /**
     * @var \Shopsys\FrameworkBundle\Component\Elasticsearch\ElasticsearchStructureManager
     */
    private $structureManager;

    /**
     * @var \Elasticsearch\Client
     */
    private $client;

    /**
     * @var \Elasticsearch\Namespaces\IndicesNamespace|\PHPUnit\Framework\MockObject\MockObject
     */
    private $indices;

    protected function setUp()
    {
        $definitionDirectory = __DIR__ . '/Resources';
        $this->client = $this->createMock(Client::class);
        $this->indices = $this->createMock(IndicesNamespace::class);
        $this->client->method('indices')->willReturn($this->indices);
        $this->structureManager = new ElasticsearchStructureManager($definitionDirectory, '', $this->client, '12345');
    }

    public function testCreateSuccessIndexWithAlias(): void
    {
        $expected = [
            'index' => '12345test-product1',
            'body' => [
                'settings' => [
                    'index' => [
                        'number_of_shards' => 1,
                    ],
                ],
            ],
        ];
        $this->indices->expects($this->once())->method('create')->with($expected);

        $this->structureManager->createIndex(1, static::ELASTICSEARCH_INDEX);
        $this->structureManager->createAliasForIndex(1, static::ELASTICSEARCH_INDEX);
    }

    public function testCreateWhileJsonNotExistsFails(): void
    {
        $this->expectException(ElasticsearchStructureException::class);
        $this->structureManager->createIndex(3, static::ELASTICSEARCH_INDEX);
    }

    public function testCreateWhileJsonIsNotValidFails(): void
    {
        $this->expectException(ElasticsearchStructureException::class);
        $this->structureManager->createIndex(0, static::ELASTICSEARCH_INDEX);
    }

    public function testDeleteSuccess(): void
    {
        $this->indices->method('exists')->willReturn(true);

        $expectedDelete = ['index' => 'test-product1'];
        $this->indices->expects($this->once())->method('delete')->with($expectedDelete);

        $this->structureManager->deleteCurrentIndex(1, static::ELASTICSEARCH_INDEX);
    }

    public function testDeleteNotExisting(): void
    {
        $this->indices->method('exists')->willReturn(false);

        $this->indices->expects($this->never())->method('delete');

        $this->structureManager->deleteCurrentIndex(1, static::ELASTICSEARCH_INDEX);
    }
}
