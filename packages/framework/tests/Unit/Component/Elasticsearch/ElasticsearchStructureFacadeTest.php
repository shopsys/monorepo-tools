<?php

namespace Tests\FrameworkBundle\Unit\Component\Elasticsearch;

use Elasticsearch\Client;
use Elasticsearch\Namespaces\IndicesNamespace;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Elasticsearch\ElasticsearchStructureFacade;
use Shopsys\FrameworkBundle\Component\Elasticsearch\Exception\ElasticsearchStructureException;
use Symfony\Component\Console\Output\OutputInterface;

class ElasticsearchStructureFacadeTest extends TestCase
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain|\PHPUnit\Framework\MockObject\MockObject
     */
    private $domain;

    /**
     * @var \Elasticsearch\Client|\PHPUnit\Framework\MockObject\MockObject
     */
    private $client;

    /**
     * @var \Elasticsearch\Namespaces\IndicesNamespace|\PHPUnit\Framework\MockObject\MockObject
     */
    private $indices;

    /**
     * @var ElasticsearchStructureFacade
     */
    private $facade;

    protected function setUp()
    {
        $definitionDirectory = __DIR__ . '/Resources';
        $this->client = $this->createMock(Client::class);
        $this->domain = $this->createMock(Domain::class);
        $this->indices = $this->createMock(IndicesNamespace::class);
        $this->client->method('indices')->willReturn($this->indices);
        $this->facade = new ElasticsearchStructureFacade($definitionDirectory, $this->client, $this->domain);
    }

    public function testCreateSuccessIndex(): void
    {
        $this->domain->method('getAllIds')->willReturn([1, 2]);

        $expected1 = [
            'index' => 1,
            'body' => [
                'settings' => [
                    'index' => [
                        'number_of_shards' => 1,
                    ],
                ],
            ],
        ];

        $expected2 = [
            'index' => 2,
            'body' => [
                'settings' => [
                    'index' => [
                        'number_of_shards' => 2,
                    ],
                ],
            ],
        ];
        $this->indices->method('create')->withConsecutive([$expected1], [$expected2]);

        $output = $this->createOutput(4);

        $this->facade->createIndexes($output);
    }

    public function testCreateWhileJsonNotExistsFails(): void
    {
        $this->domain->method('getAllIds')->willReturn([5]);
        $output = $this->createOutput(1);

        $this->expectException(ElasticsearchStructureException::class);
        $this->facade->createIndexes($output);
    }

    public function testCreateWhileJsonIsNotValidFails(): void
    {
        $this->domain->method('getAllIds')->willReturn([0]);
        $output = $this->createOutput(1);

        $this->expectException(ElasticsearchStructureException::class);
        $this->facade->createIndexes($output);
    }

    public function testDeleteSuccess(): void
    {
        $this->domain->method('getAllIds')->willReturn([1, 2]);
        $output = $this->createOutput(4);

        $this->indices->method('exists')->willReturn(true);

        $expectedDelete1 = ['index' => 1];
        $expectedDelete2 = ['index' => 2];

        $this->indices->method('delete')->withConsecutive([$expectedDelete1], [$expectedDelete2]);

        $this->facade->deleteIndexes($output);
    }

    public function testDeleteOnlyExistingIndex(): void
    {
        $this->domain->method('getAllIds')->willReturn([1, 2]);
        $output = $this->createOutput(4);

        $map = [
            [['index' => 1], true],
            [['index' => 2], false],
        ];
        $this->indices->method('exists')->will($this->returnValueMap($map));

        $this->indices->method('delete')->with(['index' => 1]);

        $this->facade->deleteIndexes($output);
    }

    /**
     * @param int $writelnCalledAtLeast
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    protected function createOutput(int $writelnCalledAtLeast): MockObject
    {
        $output = $this->createMock(OutputInterface::class);
        $output->expects($this->atLeast($writelnCalledAtLeast))->method('writeln');
        return $output;
    }
}
