<?php

namespace Shopsys\FrameworkBundle\Command;

use Shopsys\FrameworkBundle\Component\Elasticsearch\Exception\ElasticsearchStructureException;
use Shopsys\FrameworkBundle\Model\Product\Search\Export\ProductSearchExportStructureFacade;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ProductSearchCreateStructureCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'shopsys:product-search:create-structure';

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Search\Export\ProductSearchExportStructureFacade
     */
    protected $productSearchExportStructureFacade;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Search\Export\ProductSearchExportStructureFacade $productSearchExportStructureFacade
     */
    public function __construct(ProductSearchExportStructureFacade $productSearchExportStructureFacade)
    {
        $this->productSearchExportStructureFacade = $productSearchExportStructureFacade;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Creates structure in Elasticsearch for searching');
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
        } catch (ElasticsearchStructureException $e) {
            $symfonyStyleIo->error($e->getMessage());
        }
    }
}
