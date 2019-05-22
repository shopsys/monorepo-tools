<?php

namespace Shopsys\FrameworkBundle\Component\Elasticsearch;

use Elasticsearch\Client;
use Shopsys\FrameworkBundle\Component\Elasticsearch\Exception\ElasticsearchStructureException;

class ElasticsearchStructureManager
{
    /**
     * @var string
     */
    protected $definitionDirectory;

    /**
     * @var string
     */
    protected $indexPrefix;

    /**
     * @var \Elasticsearch\Client
     */
    protected $client;

    /**
     * @param string $definitionDirectory
     * @param string $indexPrefix
     * @param \Elasticsearch\Client $client
     */
    public function __construct(string $definitionDirectory, string $indexPrefix, Client $client)
    {
        $this->definitionDirectory = $definitionDirectory;
        $this->indexPrefix = $indexPrefix;
        $this->client = $client;
    }

    /**
     * @param int $domainId
     * @param string $index
     * @return string
     */
    public function getIndexName(int $domainId, string $index): string
    {
        return $this->indexPrefix . $index . $domainId;
    }

    /**
     * @param int $domainId
     * @param string $index
     */
    public function createIndex(int $domainId, string $index)
    {
        $definition = $this->getDefinition($domainId, $index);
        $indexes = $this->client->indices();
        $indexName = $this->getIndexName($domainId, $index);
        if ($indexes->exists(['index' => $indexName])) {
            throw new ElasticsearchStructureException(sprintf('Index %s already exists', $indexName));
        }

        $indexes->create([
            'index' => $indexName,
            'body' => $definition,
        ]);
    }

    /**
     * @param int $domainId
     * @param string $index
     */
    public function deleteIndex(int $domainId, string $index)
    {
        $indexes = $this->client->indices();
        $indexName = $this->getIndexName($domainId, $index);
        if ($indexes->exists(['index' => $indexName])) {
            $indexes->delete(['index' => $indexName]);
        }
    }

    /**
     * @param int $domainId
     * @param string $index
     * @return array
     */
    protected function getDefinition(int $domainId, string $index): array
    {
        $file = sprintf('%s/%s/%s.json', $this->definitionDirectory, $index, $domainId);
        if (!is_file($file)) {
            throw new ElasticsearchStructureException(
                sprintf(
                    'Definition file %d.json, for domain ID %1$d, not found in definition folder "%s".' . PHP_EOL . 'Please make sure that for each domain exists a definition json file named by the corresponding domain ID.',
                    $domainId,
                    $this->definitionDirectory
                )
            );
        }
        $json = file_get_contents($file);

        $definition = json_decode($json, JSON_OBJECT_AS_ARRAY);
        if ($definition === null) {
            throw new ElasticsearchStructureException(sprintf('Invalid JSON format in file %s', $file));
        }

        return $definition;
    }
}
