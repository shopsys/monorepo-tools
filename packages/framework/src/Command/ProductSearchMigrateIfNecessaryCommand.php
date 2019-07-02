<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Command;

use Shopsys\FrameworkBundle\Model\Product\Search\Export\ProductSearchExportStructureFacade;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ProductSearchMigrateIfNecessaryCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'shopsys:product-search:migrate-if-necessary';

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
            ->setDescription('Creates new structure, reindexes it from old one, deletes old structure and adds alias to new structure');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $symfonyStyleIo = new SymfonyStyle($input, $output);
        $output->writeln('Migrating indexes');
        $this->productSearchExportStructureFacade->migrateIndexesIfNecessary($output);
        $symfonyStyleIo->success('Indexes migrated successfully!');
    }
}
