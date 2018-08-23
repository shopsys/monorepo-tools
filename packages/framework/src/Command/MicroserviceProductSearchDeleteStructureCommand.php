<?php

namespace Shopsys\FrameworkBundle\Command;

use Shopsys\FrameworkBundle\Component\Microservice\ProductSearchExport\ProductSearchExportStructureFacade;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MicroserviceProductSearchDeleteStructureCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'shopsys:microservice:product-search:delete-structure';

    /**
     * @var ProductSearchExportStructureFacade
     */
    private $productSearchExportStructureFacade;

    /**
     * @param \Shopsys\FrameworkBundle\Component\Microservice\ProductSearchExport\ProductSearchExportStructureFacade $productSearchExportStructureFacade
     */
    public function __construct(ProductSearchExportStructureFacade $productSearchExportStructureFacade)
    {
        $this->productSearchExportStructureFacade = $productSearchExportStructureFacade;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Deletes structure for searching via microservice');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $symfonyStyleIo = new SymfonyStyle($input, $output);
        $output->writeln('Deleting structure');
        $this->productSearchExportStructureFacade->deleteIndexes($output);
        $symfonyStyleIo->success('Structure deleted successfully!');
    }
}
