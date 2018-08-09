<?php

namespace Shopsys\FrameworkBundle\Component\Elasticsearch;

use Elasticsearch\Client;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Elasticsearch\Exception\ElasticsearchStructureException;
use Symfony\Component\Console\Output\OutputInterface;

class ElasticsearchStructureFacade
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
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    protected $domain;

    /**
     * @param string $definitionDirectory
     * @param \Elasticsearch\Client $client
     * @param \Shopsys\FrameworkBundle\Component\Domain\Domain $domain
     */
    public function __construct(string $definitionDirectory, Client $client, Domain $domain)
    {
        $this->definitionDirectory = $definitionDirectory;
        $this->client = $client;
        $this->domain = $domain;
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    public function createIndexes(OutputInterface $output)
    {
        foreach ($this->domain->getAllIds() as $domainId) {
            $output->writeln(sprintf('Creating index for id %s', $domainId));
            $definition = $this->getDefinition($domainId);
            $indexes = $this->client->indices();
            $indexes->create([
                'index' => (string)$domainId,
                'body' => $definition,
            ]);
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
            $indexes = $this->client->indices();
            if ($indexes->exists(['index' => (string)$domainId])) {
                $indexes->delete(['index' => (string)$domainId]);
                $output->writeln(sprintf('Index %s deleted', $domainId));
            } else {
                $output->writeln(sprintf('Index %s not found, skipping', $domainId));
            }
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
            throw new ElasticsearchStructureException(sprintf('File %s not found', $file));
        }
        $json = file_get_contents($file);

        $definition = json_decode($json, JSON_OBJECT_AS_ARRAY);
        if ($definition === null) {
            throw new ElasticsearchStructureException(sprintf('Invalid JSON format in file %s', $file));
        }

        return $definition;
    }
}
