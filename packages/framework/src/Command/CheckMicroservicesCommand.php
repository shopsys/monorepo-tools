<?php

namespace Shopsys\FrameworkBundle\Command;

use Shopsys\FrameworkBundle\Component\Microservice\MicroserviceClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckMicroservicesCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'shopsys:microservices-check';

    /**
     * @var \Shopsys\FrameworkBundle\Component\Microservice\MicroserviceClient
     */
    protected $microserviceProductSearchClient;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Microservice\MicroserviceClient
     */
    protected $microserviceProductSearchExportClient;

    public function __construct(
        MicroserviceClient $microserviceProductSearchClient,
        MicroserviceClient $microserviceProductSearchExportClient
    ) {
        $this->microserviceProductSearchClient = $microserviceProductSearchClient;
        $this->microserviceProductSearchExportClient = $microserviceProductSearchExportClient;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Checks availability of microservices');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->checkAvailabilityOfMicroserviceProductSearch($output);
        $this->checkAvailabilityOfMicroserviceProductSearchExport($output);
    }

    private function checkAvailabilityOfMicroserviceProductSearch(OutputInterface $output)
    {
        $output->writeln('Checks availability of Microservice Product Search...');

        try {
            $resource = sprintf('%s/search-product-ids', 1);
            $this->microserviceProductSearchClient->get($resource, [
                'searchText' => '',
            ]);
        } catch (\GuzzleHttp\Exception\ConnectException $ex) {
            $message = 'Microservice Product Search is unvailable!';
            throw new \Shopsys\FrameworkBundle\Command\Exception\UnavailableMicroserviceException($message, 0, $ex);
        }

        $output->writeln('Microservice Product Search is available');
    }

    private function checkAvailabilityOfMicroserviceProductSearchExport(OutputInterface $output)
    {
        $output->writeln('Checks availability of Microservice Product Search Export...');

        try {
            $this->microserviceProductSearchExportClient->get('/', []);
        } catch (\GuzzleHttp\Exception\ConnectException $ex) {
            $message = 'Microservice Product Search Export is unvailable!';
            throw new \Shopsys\FrameworkBundle\Command\Exception\UnavailableMicroserviceException($message, 0, $ex);
        }

        $output->writeln('Microservice Product Search Export is available');
    }
}
