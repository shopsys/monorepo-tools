<?php

namespace Shopsys\FrameworkBundle\Command;

use Shopsys\FrameworkBundle\Component\Microservice\ProductSearchExport\ProductSearchExportStructureFacade;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MicroserviceProductSearchCreateStructureCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'shopsys:microservice:product-search:create-structure';

    /**
     * @var \Shopsys\FrameworkBundle\Component\Microservice\ProductSearchExport\ProductSearchExportStructureFacade
     */
    private $productSearchExportStructureFacade;

    public function __construct(ProductSearchExportStructureFacade $productSearchExportStructureFacade)
    {
        $this->productSearchExportStructureFacade = $productSearchExportStructureFacade;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Creates structure for searching via microservice');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $symfonyStyleIo = new SymfonyStyle($input, $output);
        $output->writeln('Creating structure');
        try {
            $this->productSearchExportStructureFacade->createIndexes($output);
            $symfonyStyleIo->success('Structure created successfully!');
        } catch (\Shopsys\FrameworkBundle\Component\Microservice\ProductSearchExport\Exception\ProductSearchExportStructureException $e) {
            $symfonyStyleIo->error($e->getMessage());
        }
    }
}
