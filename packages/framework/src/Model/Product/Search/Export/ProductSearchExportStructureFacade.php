<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Model\Product\Search\Export;

use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Elasticsearch\ElasticsearchStructureManager;
use Shopsys\FrameworkBundle\Component\Elasticsearch\ElasticsearchStructureUpdateChecker;
use Shopsys\FrameworkBundle\Model\Product\Search\ProductElasticsearchRepository;
use Symfony\Component\Console\Output\OutputInterface;

class ProductSearchExportStructureFacade
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    protected $domain;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Elasticsearch\ElasticsearchStructureManager
     */
    protected $elasticsearchStructureManager;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Elasticsearch\ElasticsearchStructureUpdateChecker
     */
    protected $elasticsearchStructureUpdateChecker;

    /**
     * @param \Shopsys\FrameworkBundle\Component\Domain\Domain $domain
     * @param \Shopsys\FrameworkBundle\Component\Elasticsearch\ElasticsearchStructureManager $elasticsearchStructureManager
     * @param \Shopsys\FrameworkBundle\Component\Elasticsearch\ElasticsearchStructureUpdateChecker $elasticsearchStructureUpdateChecker
     */
    public function __construct(
        Domain $domain,
        ElasticsearchStructureManager $elasticsearchStructureManager,
        ElasticsearchStructureUpdateChecker $elasticsearchStructureUpdateChecker
    ) {
        $this->domain = $domain;
        $this->elasticsearchStructureManager = $elasticsearchStructureManager;
        $this->elasticsearchStructureUpdateChecker = $elasticsearchStructureUpdateChecker;
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    public function migrateIndexesIfNecessary(OutputInterface $output)
    {
        foreach ($this->domain->getAllIds() as $domainId) {
            $output->writeln(sprintf('Migrating index for domain with ID %s', $domainId));

            $index = ProductElasticsearchRepository::ELASTICSEARCH_INDEX;

            if ($this->elasticsearchStructureUpdateChecker->isNecessaryToUpdateStructure($domainId, $index)) {
                $this->elasticsearchStructureManager->migrateIndex($domainId, $index);
                $output->writeln(sprintf('Migration done for domain with ID %s', $domainId));
            } else {
                $output->writeln(sprintf('Migrating is not necessary as there were no changes in the definition'));
            }
        }
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    public function createIndexes(OutputInterface $output)
    {
        foreach ($this->domain->getAllIds() as $domainId) {
            $this->createIndexIfNecessary($output, $domainId);
            $this->createAlias($output, $domainId);
        }
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param int $domainId
     */
    protected function createAlias(OutputInterface $output, int $domainId)
    {
        $output->writeln(sprintf('Creating alias for domain with ID %s', $domainId));

        $this->elasticsearchStructureManager->createAliasForIndex(
            $domainId,
            ProductElasticsearchRepository::ELASTICSEARCH_INDEX
        );
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param int $domainId
     */
    protected function createIndexIfNecessary(OutputInterface $output, int $domainId)
    {
        $output->writeln(sprintf('Creating index for domain with ID %s', $domainId));

        $index = ProductElasticsearchRepository::ELASTICSEARCH_INDEX;
        if ($this->elasticsearchStructureUpdateChecker->isNecessaryToUpdateStructure($domainId, $index)) {
            $this->elasticsearchStructureManager->createIndex($domainId, $index);
            $output->writeln('Index created');
        } else {
            $output->writeln('Creating index was not necessary as the structure did not change');
        }
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    public function deleteIndexes(OutputInterface $output)
    {
        foreach ($this->domain->getAllIds() as $domainId) {
            $output->writeln(sprintf('Deleting index for domain with ID %s', $domainId));
            $this->elasticsearchStructureManager->deleteCurrentIndex(
                $domainId,
                ProductElasticsearchRepository::ELASTICSEARCH_INDEX
            );
        }
    }
}
