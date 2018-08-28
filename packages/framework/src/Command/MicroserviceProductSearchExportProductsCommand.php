<?php

namespace Shopsys\FrameworkBundle\Command;

use Shopsys\FrameworkBundle\Model\Product\ProductSearchExport\ProductSearchExportFacade;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MicroserviceProductSearchExportProductsCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'shopsys:microservice:product-search:export-products';

    /**
     * @var ProductSearchExportFacade
     */
    protected $exportFacade;

    public function __construct(ProductSearchExportFacade $exportFacade)
    {
        parent::__construct();
        $this->exportFacade = $exportFacade;
    }

    protected function configure()
    {
        $this
            ->setDescription('Exports all products for searching via microservice');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $symfonyStyleIo = new SymfonyStyle($input, $output);
        $output->writeln('Exporting products to microservice');
        $this->exportFacade->exportAll();
        $symfonyStyleIo->success('All products successfully exported');
    }
}
