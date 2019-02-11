<?php

namespace Shopsys\FrameworkBundle\Model\Product\Search\Export;

use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Elasticsearch\ElasticsearchStructureManager;
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
     * @param \Shopsys\FrameworkBundle\Component\Domain\Domain $domain
     * @param \Shopsys\FrameworkBundle\Component\Elasticsearch\ElasticsearchStructureManager $elasticsearchStructureManager
     */
    public function __construct(Domain $domain, ElasticsearchStructureManager $elasticsearchStructureManager)
    {
        $this->domain = $domain;
        $this->elasticsearchStructureManager = $elasticsearchStructureManager;
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    public function createIndexes(OutputInterface $output)
    {
        foreach ($this->domain->getAllIds() as $domainId) {
            $output->writeln(sprintf('Creating index for id %s', $domainId));

            $this->elasticsearchStructureManager->createIndex(
                $domainId,
                ProductElasticsearchRepository::ELASTICSEARCH_INDEX
            );

            $output->writeln('Index created');
        }
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    public function deleteIndexes(OutputInterface $output)
    {
        foreach ($this->domain->getAllIds() as $domainId) {
            $output->writeln(sprintf('Deleting index for id %s', $domainId));
            $this->elasticsearchStructureManager->deleteIndex(
                $domainId,
                ProductElasticsearchRepository::ELASTICSEARCH_INDEX
            );
        }
    }
}
