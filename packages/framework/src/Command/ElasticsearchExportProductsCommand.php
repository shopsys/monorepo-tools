<?php

namespace Shopsys\FrameworkBundle\Command;

use Shopsys\FrameworkBundle\Model\Product\Elasticsearch\ElasticsearchExportProductFacade;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ElasticsearchExportProductsCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'shopsys:elasticsearch:export-products';

    /**
     * @var ElasticsearchExportProductFacade
     */
    protected $exportFacade;

    public function __construct(ElasticsearchExportProductFacade $exportFacade)
    {
        parent::__construct();
        $this->exportFacade = $exportFacade;
    }

    protected function configure()
    {
        $this
            ->setDescription('Export all products to the elasticsearch');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $symfonyStyleIo = new SymfonyStyle($input, $output);
        $output->writeln('Exporting products to the elasticsearch');
        $this->exportFacade->exportAll();
        $symfonyStyleIo->success('All products successfully exported');
    }
}
