<?php

namespace Shopsys\FrameworkBundle\Component\Microservice\ProductSearchExport;

use GuzzleHttp\Exception\ServerException;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Microservice\MicroserviceClient;
use Shopsys\FrameworkBundle\Component\Microservice\ProductSearchExport\Exception\ProductSearchExportStructureException;
use Symfony\Component\Console\Output\OutputInterface;

class ProductSearchExportStructureFacade
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Microservice\MicroserviceClient
     */
    protected $microserviceClient;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    protected $domain;

    /**
     * @param \Shopsys\FrameworkBundle\Component\Microservice\MicroserviceClient $microserviceClient
     * @param \Shopsys\FrameworkBundle\Component\Domain\Domain $domain
     */
    public function __construct(MicroserviceClient $microserviceClient, Domain $domain)
    {
        $this->microserviceClient = $microserviceClient;
        $this->domain = $domain;
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    public function createIndexes(OutputInterface $output)
    {
        foreach ($this->domain->getAllIds() as $domainId) {
            $output->writeln(sprintf('Creating index for id %s', $domainId));
            try {
                $this->microserviceClient->post($domainId);
            } catch (ServerException $e) {
                $response = (string)$e->getResponse()->getBody();
                $jsonResponse = json_decode($response, true);
                if ($jsonResponse && isset($jsonResponse['message'])) {
                    throw new ProductSearchExportStructureException($jsonResponse['message']);
                } else {
                    throw new ProductSearchExportStructureException($e->getMessage());
                }
            }
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
            $this->microserviceClient->delete($domainId);
        }
    }
}
