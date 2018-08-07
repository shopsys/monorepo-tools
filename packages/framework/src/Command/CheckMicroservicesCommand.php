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

    public function __construct(MicroserviceClient $microserviceProductSearchClient)
    {
        $this->microserviceProductSearchClient = $microserviceProductSearchClient;

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
    }

    private function checkAvailabilityOfMicroserviceProductSearch(OutputInterface $output)
    {
        $output->writeln('Checks availability of Microservice Product Search...');

        try {
            $this->microserviceProductSearchClient->get('search-product-ids', [
                'searchText' => '',
                'domainId' => 1,
            ]);
        } catch (\GuzzleHttp\Exception\ConnectException $ex) {
            $message = 'Microservice Product Search is unvailable!';
            throw new \Shopsys\FrameworkBundle\Command\Exception\UnavailableMicroserviceException($message, 0, $ex);
        }

        $output->writeln('Microservice Product Search is available');
    }
}
