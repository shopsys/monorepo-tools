<?php

declare(strict_types=1);

namespace Tests\ShopBundle\Functional\Component\Elasticsearch;

use Elasticsearch\Client;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Elasticsearch\ElasticsearchStructureManager;
use Shopsys\FrameworkBundle\Component\Elasticsearch\ElasticsearchStructureUpdateChecker;
use Shopsys\FrameworkBundle\Model\Product\Search\ProductElasticsearchRepository;
use Tests\ShopBundle\Test\FunctionalTestCase;

final class ElasticsearchStructureUpdateCheckerTest extends FunctionalTestCase
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Elasticsearch\ElasticsearchStructureUpdateChecker
     */
    private $elasticsearchStructureUpdateChecker;

    /**
     * @var \Elasticsearch\Client
     */
    private $elasticsearchClient;

    /**
     * @var \Elasticsearch\Namespaces\IndicesNamespace
     */
    private $elasticsearchIndexes;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Elasticsearch\ElasticsearchStructureManager
     */
    private $elasticsearchStructureManager;

    public function setUp(): void
    {
        parent::setUp();

        $this->elasticsearchStructureUpdateChecker = $this->getContainer()->get(ElasticsearchStructureUpdateChecker::class);

        $this->elasticsearchClient = $this->getContainer()->get(Client::class);
        $this->elasticsearchIndexes = $this->elasticsearchClient->indices();
        $this->elasticsearchStructureManager = $this->getContainer()->get(ElasticsearchStructureManager::class);
    }

    /**
     * @return iterable
     */
    public function elasticseachIndexesParametersProvider(): iterable
    {
        /** @var \Shopsys\FrameworkBundle\Component\Domain\Domain $domain */
        $domain = $this->getContainer()->get(Domain::class);

        foreach ($domain->getAllIds() as $domainId) {
            yield [$domainId, ProductElasticsearchRepository::ELASTICSEARCH_INDEX];
        }
    }

    /**
     * @param int $domainId
     * @param string $index
     * @dataProvider elasticseachIndexesParametersProvider
     */
    public function testUpdateIsNotNecessaryWhenNothingIsChanged(int $domainId, string $index): void
    {
        $definition = $this->elasticsearchStructureManager->getStructureDefinition($domainId, $index);
        $aliasName = $this->elasticsearchStructureManager->getAliasName($domainId, $index);
        $indexName = $this->elasticsearchStructureManager->getCurrentIndexName($domainId, $index);

        $this->createNewStructureAndMakeBackup($definition, $definition, $indexName, $aliasName);

        try {
            $this->assertFalse($this->elasticsearchStructureUpdateChecker->isNecessaryToUpdateStructure($domainId, $index));
        } finally {
            $this->revertStructureFromBackup($definition, $indexName, $aliasName);
        }
    }

    /**
     * @param int $domainId
     * @param string $index
     * @dataProvider elasticseachIndexesParametersProvider
     */
    public function testUpdateIsNecessaryWhenStructureHasAdditionalProperty(int $domainId, string $index): void
    {
        $oldDefinition = $this->elasticsearchStructureManager->getStructureDefinition($domainId, $index);
        $aliasName = $this->elasticsearchStructureManager->getAliasName($domainId, $index);
        $indexName = $this->elasticsearchStructureManager->getCurrentIndexName($domainId, $index);

        $newDefinition = $oldDefinition;
        $newDefinition['mappings']['_doc']['properties']['new_property'] = ['type' => 'text'];

        $this->createNewStructureAndMakeBackup($oldDefinition, $newDefinition, $indexName, $aliasName);

        try {
            $this->assertTrue($this->elasticsearchStructureUpdateChecker->isNecessaryToUpdateStructure($domainId, $index));
        } finally {
            $this->revertStructureFromBackup($oldDefinition, $indexName, $aliasName);
        }
    }

    /**
     * @param array $oldDefinition
     * @param array $newDefinition
     * @param string $indexName
     * @param string $aliasName
     */
    private function createNewStructureAndMakeBackup(array $oldDefinition, array $newDefinition, string $indexName, string $aliasName): void
    {
        $backupIndexName = $indexName . '_backup';
        $this->moveStructureByReindexing($indexName, $backupIndexName, $oldDefinition);
        $this->elasticsearchIndexes->create(['index' => $indexName, 'body' => $newDefinition]);
        $this->elasticsearchIndexes->putAlias(['index' => $indexName, 'name' => $aliasName]);
    }

    /**
     * @param array $oldDefinition
     * @param string $indexName
     * @param string $aliasName
     */
    private function revertStructureFromBackup(array $oldDefinition, string $indexName, string $aliasName): void
    {
        $backupIndexName = $indexName . '_backup';
        $this->elasticsearchIndexes->delete(['index' => $indexName]);
        $this->moveStructureByReindexing($backupIndexName, $indexName, $oldDefinition);
        $this->elasticsearchIndexes->putAlias(['index' => $indexName, 'name' => $aliasName]);
    }

    /**
     * @param string $oldName
     * @param string $newName
     * @param array $definition
     */
    private function moveStructureByReindexing(string $oldName, string $newName, array $definition): void
    {
        $this->elasticsearchIndexes->create([
            'index' => $newName,
            'body' => $definition,
        ]);
        $this->elasticsearchClient->reindex([
            'body' => [
                'source' => ['index' => $oldName],
                'dest' => ['index' => $newName],
            ],
            'refresh' => true,
            'wait_for_completion' => true,
        ]);

        $this->elasticsearchIndexes->delete(['index' => $oldName]);
    }
}
