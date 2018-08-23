<?php

namespace Shopsys\MicroserviceProductSearchExport\Structure;

use Elasticsearch\Client;
use Shopsys\MicroserviceProductSearchExport\Structure\Exception\StructureException;

class StructureManager
{
    /**
     * @var string
     */
    protected $definitionDirectory;

    /**
     * @var \Elasticsearch\Client
     */
    protected $client;

    /**
     * @param string $definitionDirectory
     * @param \Elasticsearch\Client $client
     */
    public function __construct(string $definitionDirectory, Client $client)
    {
        $this->definitionDirectory = $definitionDirectory;
        $this->client = $client;
    }

    /**
     * @param int $domainId
     */
    public function createIndex(int $domainId)
    {
        $definition = $this->getDefinition($domainId);
        $indexes = $this->client->indices();
        if ($indexes->exists(['index' => (string)$domainId])) {
            throw new StructureException(sprintf('Index %s already exists', $domainId));
        }
        $indexes->create([
            'index' => (string)$domainId,
            'body' => $definition,
        ]);
    }

    /**
     * @param int $domainId
     */
    public function deleteIndex(int $domainId)
    {
        $indexes = $this->client->indices();
        if ($indexes->exists(['index' => (string)$domainId])) {
            $indexes->delete(['index' => (string)$domainId]);
        }
    }

    /**
     * @param int $domainId
     * @return array
     */
    protected function getDefinition(int $domainId): array
    {
        $file = sprintf('%s/%s.json', $this->definitionDirectory, $domainId);
        if (!is_file($file)) {
            throw new StructureException(sprintf('File %s not found', $file));
        }
        $json = file_get_contents($file);

        $definition = json_decode($json, JSON_OBJECT_AS_ARRAY);
        if ($definition === null) {
            throw new StructureException(sprintf('Invalid JSON format in file %s', $file));
        }

        return $definition;
    }
}
