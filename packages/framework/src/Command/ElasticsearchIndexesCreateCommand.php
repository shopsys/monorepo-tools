<?php

namespace Shopsys\FrameworkBundle\Command;

use Shopsys\FrameworkBundle\Component\Elasticsearch\ElasticsearchStructureFacade;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ElasticsearchIndexesCreateCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'shopsys:elasticsearch:create-indexes';

    /**
     * @var \Shopsys\FrameworkBundle\Component\Elasticsearch\ElasticsearchStructureFacade
     */
    private $elasticsearchStructureFacade;

    public function __construct(ElasticsearchStructureFacade $elasticsearchStructureFacade)
    {
        $this->elasticsearchStructureFacade = $elasticsearchStructureFacade;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Create Elasticsearch indexes structure');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $symfonyStyleIo = new SymfonyStyle($input, $output);
        $output->writeln('Creating indexes structure');
        try {
            $this->elasticsearchStructureFacade->createIndexes($output);
            $symfonyStyleIo->success('Indexes created successfully!');
        } catch (\Shopsys\FrameworkBundle\Component\Elasticsearch\Exception\ElasticsearchStructureException $e) {
            $symfonyStyleIo->error($e->getMessage());
        }
    }
}
