<?php

namespace Shopsys\FrameworkBundle\Component\Elasticsearch;

use BadMethodCallException;
use Elasticsearch\Client;
use Shopsys\FrameworkBundle\Component\Elasticsearch\Exception\ElasticsearchMoreThanOneCurrentIndexException;
use Shopsys\FrameworkBundle\Component\Elasticsearch\Exception\ElasticsearchNoCurrentIndexException;
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
     * @var string|null
     */
    protected $buildVersion;

    /**
     * @param string $definitionDirectory
     * @param string $indexPrefix
     * @param \Elasticsearch\Client $client
     * @param string|null $buildVersion
     */
    public function __construct(string $definitionDirectory, string $indexPrefix, Client $client, ?string $buildVersion = null)
    {
        $this->definitionDirectory = $definitionDirectory;
        $this->indexPrefix = $indexPrefix;
        $this->client = $client;
        $this->buildVersion = $buildVersion;
    }

    /**
     * @param string $buildVersion
     * @internal Will be replaced with constructor injection in the next major release
     */
    public function setBuildVersion(string $buildVersion): void
    {
        if ($this->buildVersion !== null) {
            throw new BadMethodCallException(sprintf('Method "%s" has been already called and cannot be called multiple times.', __METHOD__));
        }

        $this->buildVersion = $buildVersion;
    }

    /**
     * @param int $domainId
     * @param string $index
     * @return string
     */
    protected function getVersionedIndexName(int $domainId, string $index): string
    {
        return $this->indexPrefix . ($this->buildVersion ?? 'null') . $index . $domainId;
    }

    /**
     * @param int $domainId
     * @param string $index
     * @return string
     */
    public function getCurrentIndexName(int $domainId, string $index): string
    {
        $aliasName = $this->getAliasName($domainId, $index);

        $indexNames = $this->getExistingIndexNamesForAlias($aliasName);

        if (count($indexNames) > 1) {
            throw new ElasticsearchMoreThanOneCurrentIndexException($aliasName);
        } elseif (count($indexNames) === 0) {
            throw new ElasticsearchNoCurrentIndexException($aliasName);
        }

        return reset($indexNames);
    }

    /**
     * @param string $aliasName
     * @return array
     */
    protected function getExistingIndexNamesForAlias(string $aliasName): array
    {
        $existingIndexNames = [];
        $indexes = $this->client->indices();

        if ($indexes->existsAlias(['name' => $aliasName])) {
            $aliases = $indexes->getAlias([
                'name' => $aliasName,
            ]);
            foreach (array_keys($aliases) as $indexName) {
                if ($indexes->exists(['index' => $indexName])) {
                    $existingIndexNames[] = $indexName;
                }
            }
        }

        return $existingIndexNames;
    }

    /**
     * @param int $domainId
     * @param string $index
     * @return string
     */
    public function getAliasName(int $domainId, string $index): string
    {
        return $this->indexPrefix . $index . $domainId;
    }

    /**
     * @param int $domainId
     * @param string $index
     */
    public function createIndex(int $domainId, string $index)
    {
        $definition = $this->getStructureDefinition($domainId, $index);
        $indexes = $this->client->indices();
        $indexName = $this->getVersionedIndexName($domainId, $index);
        if ($indexes->exists(['index' => $indexName])) {
            $message = sprintf('Index %s already exists', $indexName);
            if ($this->buildVersion === '0000000000000000' || $this->buildVersion === null) {
                $message .= ' Please start using build version or delete current index before creating a new one.';
            } else {
                $message .= ' Maybe you forgot to execute "php phing generate-build-version" before creating new index?';
            }
            throw new ElasticsearchStructureException($message);
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
    public function migrateIndex(int $domainId, string $index)
    {
        $this->createIndex($domainId, $index);
        $this->reindexFromCurrentIndexToNewIndex($domainId, $index);
        $this->deleteCurrentIndex($domainId, $index);
        $this->createAliasForIndex($domainId, $index);
    }

    /**
     * @param int $domainId
     * @param string $index
     */
    protected function reindexFromCurrentIndexToNewIndex(int $domainId, string $index)
    {
        $indexes = $this->client->indices();
        $indexName = $this->getVersionedIndexName($domainId, $index);
        $currentIndexName = $this->getCurrentIndexName($domainId, $index);
        if ($indexes->exists(['index' => $currentIndexName]) && $indexes->exists(['index' => $indexName])) {
            $body = [
                'source' => [
                    'index' => $currentIndexName,
                ],
                'dest' => [
                    'index' => $indexName,
                ],
            ];

            $this->client->reindex([
                'body' => $body,
            ]);
        }
    }

    /**
     * @param int $domainId
     * @param string $index
     */
    public function createAliasForIndex(int $domainId, string $index)
    {
        $indexes = $this->client->indices();
        $aliasName = $this->getAliasName($domainId, $index);
        $indexName = $this->getVersionedIndexName($domainId, $index);

        if (!$indexes->existsAlias(['name' => $aliasName, 'index' => $indexName]) && $indexes->exists(['index' => $indexName])) {
            $indexes->putAlias([
                'index' => $indexName,
                'name' => $aliasName,
            ]);
        }
    }

    /**
     * @param int $domainId
     * @param string $index
     */
    public function deleteCurrentIndex(int $domainId, string $index): void
    {
        try {
            $indexName = $this->getCurrentIndexName($domainId, $index);
        } catch (ElasticsearchNoCurrentIndexException $e) {
            return;
        }

        $this->client->indices()->delete(['index' => $indexName]);
    }

    /**
     * @param int $domainId
     * @param string $index
     * @return array
     */
    public function getStructureDefinition(int $domainId, string $index): array
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
