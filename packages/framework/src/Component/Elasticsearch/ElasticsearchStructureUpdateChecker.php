<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Component\Elasticsearch;

use Elasticsearch\Client;
use Shopsys\FrameworkBundle\Component\ArrayUtils\RecursiveArraySorter;
use Shopsys\FrameworkBundle\Component\Elasticsearch\Exception\ElasticsearchNoCurrentIndexException;

class ElasticsearchStructureUpdateChecker
{
    /**
     * @var \Elasticsearch\Client
     */
    protected $client;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Elasticsearch\ElasticsearchStructureManager
     */
    protected $elasticsearchStructureManager;

    /**
     * @var \Shopsys\FrameworkBundle\Component\ArrayUtils\RecursiveArraySorter
     */
    protected $recursiveArraySorter;

    /**
     * @param \Elasticsearch\Client $client
     * @param \Shopsys\FrameworkBundle\Component\Elasticsearch\ElasticsearchStructureManager $elasticsearchStructureManager
     * @param \Shopsys\FrameworkBundle\Component\ArrayUtils\RecursiveArraySorter $recursiveArraySorter
     */
    public function __construct(
        Client $client,
        ElasticsearchStructureManager $elasticsearchStructureManager,
        RecursiveArraySorter $recursiveArraySorter
    ) {
        $this->client = $client;
        $this->elasticsearchStructureManager = $elasticsearchStructureManager;
        $this->recursiveArraySorter = $recursiveArraySorter;
    }

    /**
     * @param int $domainId
     * @param string $index
     * @return bool
     */
    public function isNecessaryToUpdateStructure(int $domainId, string $index): bool
    {
        $definition = $this->elasticsearchStructureManager->getStructureDefinition($domainId, $index);

        try {
            $indexName = $this->elasticsearchStructureManager->getCurrentIndexName($domainId, $index);
        } catch (ElasticsearchNoCurrentIndexException $e) {
            return true;
        }

        $currentIndex = $this->client->indices()->get(['index' => $indexName]);

        $currentIndexDefinition = $this->prepareDefinitionForComparing(reset($currentIndex));
        $definition = $this->prepareDefinitionForComparing($definition);

        return $currentIndexDefinition !== $definition;
    }

    /**
     * @param array $definition
     * @return array
     */
    protected function prepareDefinitionForComparing(array $definition): array
    {
        if (array_key_exists('analysis', $definition['settings'])) {
            $definition['settings']['index']['analysis'] = $definition['settings']['analysis'];
            unset($definition['settings']['analysis']);
        }

        unset(
            $definition['aliases'],
            $definition['settings']['index']['creation_date'],
            $definition['settings']['index']['provided_name'],
            $definition['settings']['index']['uuid'],
            $definition['settings']['index']['version'],
        );

        $this->recursiveArraySorter->recursiveArrayKsort($definition);

        return $this->castAllIntegersToStrings($definition);
    }

    /**
     * @param array $structureDefinition
     * @return array
     */
    protected function castAllIntegersToStrings(array $structureDefinition): array
    {
        return array_map(
            function ($item) {
                if (is_array($item)) {
                    return $this->castAllIntegersToStrings($item);
                }
                return is_int($item) ? (string)$item : $item;
            },
            $structureDefinition
        );
    }
}
