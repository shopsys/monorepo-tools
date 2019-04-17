<?php

namespace Shopsys\FrameworkBundle\Command;

use Shopsys\FrameworkBundle\Model\Product\Search\Export\ProductSearchExportFacade;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ProductSearchExportProductsCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'shopsys:product-search:export-products';

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Search\Export\ProductSearchExportFacade
     */
    private $productSearchExportFacade;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Search\Export\ProductSearchExportFacade $productSearchExportFacade
     */
    public function __construct(ProductSearchExportFacade $productSearchExportFacade)
    {
        parent::__construct();
        $this->productSearchExportFacade = $productSearchExportFacade;
    }

    protected function configure()
    {
        $this
            ->setDescription('Exports all products to Elasticsearch for searching ');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $symfonyStyleIo = new SymfonyStyle($input, $output);
        $output->writeln('Exporting products to Elasticsearch');
        $this->productSearchExportFacade->exportAll();
        $symfonyStyleIo->success('All products successfully exported');
    }
}
